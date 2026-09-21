<?php

namespace App\Services\Order;

use App\Const\OrderConst;
use App\Const\PaymentConst;
use App\Const\PermissionConst;
use App\Exceptions\OrderStateException;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\PaymentTransaction;
use App\Notifications\OrderCancelledByCustomer;
use App\Notifications\OrderStatusChanged;
use App\Notifications\PaymentFailed;
use App\Notifications\PaymentProblem;
use App\Notifications\PaymentReceived;
use App\Notifications\PaymentSucceeded;
use App\Notifications\RefundPending;
use App\Notifications\Refunded;
use App\Notifications\RefundRequired;
use App\Services\Client\MembershipService;
use Closure;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class OrderStateService
{
    public function __construct(
        protected OrderInventoryService $inventory,
        protected OrderNotifierService $notifier,
        protected MembershipService $membership
    ) {}

    public function transition(
        Order|string $order,
        int $to,
        string $actorType,
        ?string $actorId = null,
        ?string $note = null,
        ?Closure $guard = null
    ): Order {
        $orderId = $order instanceof Order ? $order->getKey() : $order;
        $note = filled($note) ? trim($note) : null;

        if ($actorType === OrderConst::ACTOR_ADMIN && OrderConst::noteRequiredFor($to) && $note === null) {
            throw OrderStateException::noteRequired();
        }

        return DB::transaction(function () use ($orderId, $to, $actorType, $actorId, $note, $guard) {
            $locked = $this->lock($orderId);
            $from = $locked->status;

            if (! OrderConst::canTransition($from, $to, $actorType)) {
                throw OrderStateException::invalidTransition();
            }

            if ($guard && ! $guard($locked)) {
                throw new OrderStateException(__('admin/order.messages.invalid_transition'), 'skipped');
            }

            $this->guardPayment($locked, $to);

            $now = now();
            $wasPaid = $locked->isPaid();
            $attributes = array_merge(['status' => $to], $this->timestampsFor($to, $now, $note));
            $codCollected = $to === OrderConst::STATUS_COMPLETED
                && ! $wasPaid
                && (int) $locked->payment_method === PaymentConst::METHOD_COD;

            if ($codCollected) {
                $attributes = array_merge($attributes, $this->paidAttributes($now));
            }

            $refundPending = OrderConst::isVoid($to) && $wasPaid;

            if ($refundPending) {
                $attributes = array_merge($attributes, [
                    'payment_status' => PaymentConst::STATUS_REFUND_PENDING,
                    'is_paid' => false,
                ]);
            }

            if (in_array($to, OrderConst::restockStatuses(), true)) {
                $this->inventory->restore($locked);
                $this->releaseCoupon($locked);
            }

            $affected = Order::whereKey($locked->id)
                ->where('status', $from)
                ->update(array_merge($attributes, ['updated_at' => $now]));

            if ($affected !== 1) {
                throw OrderStateException::invalidTransition();
            }

            $locked->forceFill($attributes)->syncOriginal();

            if ($codCollected) {
                $this->logTransaction($locked, [
                    'gateway' => PaymentConst::GATEWAY_COD,
                    'reference' => $locked->code,
                    'amount' => (float) $locked->total_amount,
                    'is_successful' => true,
                    'source' => $actorType === OrderConst::ACTOR_ADMIN ? PaymentConst::SOURCE_ADMIN : PaymentConst::SOURCE_SYSTEM,
                    'admin_id' => $actorType === OrderConst::ACTOR_ADMIN ? $actorId : null,
                ]);
            }

            $points = 0;

            if ($to === OrderConst::STATUS_COMPLETED) {
                $points = $this->membership->awardForOrder($locked);
            }

            if ($to === OrderConst::STATUS_RETURNED && $from === OrderConst::STATUS_COMPLETED) {
                $this->membership->reverseForOrder($locked);
            }

            $this->recordHistory($locked, OrderConst::EVENT_STATUS_CHANGED, $actorType, $actorId, $note, $from, $to);

            if ($codCollected) {
                $this->recordHistory($locked, OrderConst::EVENT_PAYMENT_PAID, $actorType, $actorId, PaymentConst::methodLabel(PaymentConst::METHOD_COD));
            }

            if ($refundPending) {
                $this->recordHistory($locked, OrderConst::EVENT_REFUND_PENDING, $actorType, $actorId);
            }

            $this->notifyTransition($locked, $from, $to, $actorType, $note, $points, $refundPending);

            return $locked;
        });
    }

    public function availableTransitions(Order $order, string $actorType): array
    {
        return array_values(array_filter(
            OrderConst::transitionsFor($order->status, $actorType),
            fn (int $status) => ! $this->blockedByPayment($order, $status)
        ));
    }

    public function blockedByPayment(Order $order, int $to): bool
    {
        return in_array($to, [OrderConst::STATUS_CONFIRMED, OrderConst::STATUS_SHIPPING, OrderConst::STATUS_COMPLETED], true)
            && PaymentConst::requiresPrepayment($order->payment_method)
            && (float) $order->total_amount > 0
            && ! $order->isPaid();
    }

    public function markPaidManually(string $orderId, string $adminId, ?string $reference = null, ?string $note = null): Order
    {
        return DB::transaction(function () use ($orderId, $adminId, $reference, $note) {
            $locked = $this->lock($orderId);

            if (OrderConst::isVoid($locked->status)) {
                throw OrderStateException::paymentState('cannot_mark_paid_void');
            }

            if (! PaymentConst::isPayable($locked->payment_status)) {
                throw OrderStateException::paymentState('already_paid');
            }

            $this->applyPaid($locked);

            $this->logTransaction($locked, [
                'gateway' => PaymentConst::GATEWAY_MANUAL,
                'reference' => filled($reference) ? trim($reference) : $locked->code,
                'amount' => (float) $locked->total_amount,
                'is_successful' => true,
                'source' => PaymentConst::SOURCE_ADMIN,
                'admin_id' => $adminId,
                'note' => $note,
            ]);

            $this->recordHistory($locked, OrderConst::EVENT_PAYMENT_PAID, OrderConst::ACTOR_ADMIN, $adminId, $note);
            $this->notifier->customer($locked, new PaymentSucceeded($locked));

            return $locked;
        });
    }

    public function markRefunded(string $orderId, string $adminId, string $note, ?string $reference = null): Order
    {
        return DB::transaction(function () use ($orderId, $adminId, $note, $reference) {
            $locked = $this->lock($orderId);

            if ((int) $locked->payment_status !== PaymentConst::STATUS_REFUND_PENDING) {
                throw OrderStateException::paymentState('not_refund_pending');
            }

            $now = now();
            $attributes = [
                'payment_status' => PaymentConst::STATUS_REFUNDED,
                'is_paid' => false,
                'is_refund' => true,
                'refunded_at' => $now,
                'refund_note' => $note,
            ];

            Order::whereKey($locked->id)->update(array_merge($attributes, ['updated_at' => $now]));
            $locked->forceFill($attributes)->syncOriginal();

            $this->logTransaction($locked, [
                'gateway' => PaymentConst::GATEWAY_MANUAL,
                'type' => PaymentConst::TYPE_REFUND,
                'reference' => filled($reference) ? trim($reference) : $locked->code,
                'amount' => (float) $locked->total_amount,
                'is_successful' => true,
                'source' => PaymentConst::SOURCE_ADMIN,
                'admin_id' => $adminId,
                'note' => $note,
            ]);

            $this->recordHistory($locked, OrderConst::EVENT_REFUNDED, OrderConst::ACTOR_ADMIN, $adminId, $note);
            $this->notifier->customer($locked, new Refunded($locked));

            return $locked;
        });
    }

    public function applyGatewaySuccess(Order $locked, string $gateway, array $transaction): string
    {
        $transaction = array_merge($transaction, ['gateway' => $gateway, 'is_successful' => true]);
        $label = PaymentConst::gatewayLabel($gateway);

        if ($locked->isPaid() || in_array((int) $locked->payment_status, [PaymentConst::STATUS_REFUND_PENDING, PaymentConst::STATUS_REFUNDED], true)) {
            $this->logTransactionSafely($locked, $transaction);
            $this->recordHistory($locked, OrderConst::EVENT_PAYMENT_PROBLEM, OrderConst::ACTOR_GATEWAY, null, __('admin/order.problems.' . PaymentConst::PROBLEM_DUPLICATE_PAYMENT));
            $this->notifier->admins(
                new PaymentProblem($locked, PaymentConst::PROBLEM_DUPLICATE_PAYMENT, $gateway, (float) $transaction['amount']),
                PermissionConst::ORDERS_REFUND
            );

            return PaymentConst::RESULT_ALREADY_CONFIRMED;
        }

        if (OrderConst::isVoid($locked->status)) {
            $now = now();
            $attributes = [
                'payment_status' => PaymentConst::STATUS_REFUND_PENDING,
                'is_paid' => false,
                'paid_at' => $now,
            ];

            Order::whereKey($locked->id)->update(array_merge($attributes, ['updated_at' => $now]));
            $locked->forceFill($attributes)->syncOriginal();

            $this->logTransaction($locked, $transaction);
            $this->recordHistory($locked, OrderConst::EVENT_REFUND_PENDING, OrderConst::ACTOR_GATEWAY, null, __('admin/order.problems.' . PaymentConst::PROBLEM_PAID_AFTER_CANCEL) . ' (' . $label . ')');
            $this->notifier->admins(
                new PaymentProblem($locked, PaymentConst::PROBLEM_PAID_AFTER_CANCEL, $gateway, (float) $transaction['amount']),
                PermissionConst::ORDERS_REFUND
            );
            $this->notifier->customer($locked, new RefundPending($locked));

            return PaymentConst::RESULT_REFUND_PENDING;
        }

        $this->applyPaid($locked);
        $this->logTransaction($locked, $transaction);
        $this->recordHistory($locked, OrderConst::EVENT_PAYMENT_PAID, OrderConst::ACTOR_GATEWAY, null, $label . ($transaction['transaction_no'] ?? null ? ' #' . $transaction['transaction_no'] : ''));
        $this->notifier->customer($locked, new PaymentSucceeded($locked));
        $this->notifier->admins(new PaymentReceived($locked, $gateway), PermissionConst::ORDERS_VIEW);

        return PaymentConst::RESULT_PAID;
    }

    public function applyGatewayFailure(Order $locked, string $gateway, array $transaction): string
    {
        $this->logTransaction($locked, array_merge($transaction, ['gateway' => $gateway, 'is_successful' => false]));

        if ((int) $locked->payment_status !== PaymentConst::STATUS_UNPAID) {
            return PaymentConst::RESULT_FAILED;
        }

        $now = now();

        Order::whereKey($locked->id)
            ->where('payment_status', PaymentConst::STATUS_UNPAID)
            ->update(['payment_status' => PaymentConst::STATUS_FAILED, 'is_paid' => false, 'updated_at' => $now]);
        $locked->forceFill(['payment_status' => PaymentConst::STATUS_FAILED, 'is_paid' => false])->syncOriginal();

        $this->recordHistory(
            $locked,
            OrderConst::EVENT_PAYMENT_FAILED,
            OrderConst::ACTOR_GATEWAY,
            null,
            PaymentConst::gatewayLabel($gateway) . (isset($transaction['response_code']) ? ' (' . $transaction['response_code'] . ')' : '')
        );

        if (! OrderConst::isVoid($locked->status)) {
            $this->notifier->customer($locked, new PaymentFailed($locked));
        }

        return PaymentConst::RESULT_FAILED;
    }

    public function applyAmountMismatch(Order $locked, string $gateway, array $transaction): string
    {
        $this->logTransaction($locked, array_merge($transaction, ['gateway' => $gateway, 'is_successful' => false]));
        $this->recordHistory($locked, OrderConst::EVENT_PAYMENT_PROBLEM, OrderConst::ACTOR_GATEWAY, null, __('admin/order.problems.' . PaymentConst::PROBLEM_AMOUNT_MISMATCH));
        $this->notifier->admins(
            new PaymentProblem($locked, PaymentConst::PROBLEM_AMOUNT_MISMATCH, $gateway, (float) ($transaction['amount'] ?? 0)),
            PermissionConst::ORDERS_REFUND
        );

        return PaymentConst::RESULT_AMOUNT_MISMATCH;
    }

    public function recordHistory(
        Order $order,
        string $event,
        string $actorType,
        ?string $actorId = null,
        ?string $note = null,
        ?int $from = null,
        ?int $to = null
    ): OrderStatusHistory {
        return OrderStatusHistory::create([
            'order_id' => $order->id,
            'from_status' => $from ?? $order->status,
            'to_status' => $to ?? $order->status,
            'event' => $event,
            'actor_type' => $actorType,
            'actor_id' => $actorId,
            'note' => $note,
        ]);
    }

    public function lock(string $orderId): Order
    {
        $order = Order::query()->lockForUpdate()->find($orderId);

        if (! $order) {
            throw OrderStateException::notFound();
        }

        return $order;
    }

    public function logTransaction(Order $order, array $data): PaymentTransaction
    {
        return PaymentTransaction::create(array_merge([
            'order_id' => $order->id,
            'type' => PaymentConst::TYPE_PAYMENT,
            'source' => PaymentConst::SOURCE_SYSTEM,
            'is_successful' => false,
            'amount' => 0,
        ], $data));
    }

    public function logTransactionSafely(Order $order, array $data): ?PaymentTransaction
    {
        try {
            return $this->logTransaction($order, $data);
        } catch (QueryException $e) {
            return null;
        }
    }

    protected function applyPaid(Order $locked): void
    {
        $now = now();
        $attributes = $this->paidAttributes($now);

        $affected = Order::whereKey($locked->id)
            ->whereIn('payment_status', PaymentConst::payableStatuses())
            ->update(array_merge($attributes, ['updated_at' => $now]));

        if ($affected !== 1) {
            throw OrderStateException::paymentState('already_paid');
        }

        $locked->forceFill($attributes)->syncOriginal();
    }

    protected function paidAttributes($now): array
    {
        return [
            'payment_status' => PaymentConst::STATUS_PAID,
            'is_paid' => true,
            'paid_at' => $now,
        ];
    }

    protected function guardPayment(Order $order, int $to): void
    {
        if ($this->blockedByPayment($order, $to)) {
            throw OrderStateException::unpaid();
        }
    }

    protected function timestampsFor(int $to, $now, ?string $note): array
    {
        return match ($to) {
            OrderConst::STATUS_CONFIRMED => ['confirmed_at' => $now],
            OrderConst::STATUS_SHIPPING => ['shipped_at' => $now],
            OrderConst::STATUS_COMPLETED => ['completed_at' => $now],
            OrderConst::STATUS_CANCELLED => ['cancelled_at' => $now, 'cancel_reason' => $note],
            OrderConst::STATUS_RETURNED => ['returned_at' => $now],
            default => [],
        };
    }

    protected function releaseCoupon(Order $order): void
    {
        if (! $order->coupon_id) {
            return;
        }

        Coupon::withTrashed()
            ->whereKey($order->coupon_id)
            ->where('usage_count', '>', 0)
            ->decrement('usage_count');

        if ($order->user_id) {
            DB::table('coupon_user')
                ->where('coupon_id', $order->coupon_id)
                ->where('user_id', $order->user_id)
                ->delete();
        }
    }

    protected function notifyTransition(
        Order $order,
        int $from,
        int $to,
        string $actorType,
        ?string $note,
        int $points,
        bool $refundPending
    ): void {
        $this->notifier->customer($order, new OrderStatusChanged($order, $from, $to, $note, $points));

        if ($actorType === OrderConst::ACTOR_CUSTOMER && $to === OrderConst::STATUS_CANCELLED) {
            $this->notifier->admins(new OrderCancelledByCustomer($order, $note), PermissionConst::ORDERS_VIEW);
        }

        if ($refundPending) {
            $this->notifier->admins(new RefundRequired($order, $to), PermissionConst::ORDERS_REFUND);
            $this->notifier->customer($order, new RefundPending($order));
        }
    }
}
