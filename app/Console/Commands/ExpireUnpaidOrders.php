<?php

namespace App\Console\Commands;

use App\Const\OrderConst;
use App\Const\PaymentConst;
use App\Exceptions\OrderStateException;
use App\Models\Order;
use App\Services\Order\OrderStateService;
use Illuminate\Console\Command;

class ExpireUnpaidOrders extends Command
{
    protected $signature = 'orders:expire-unpaid';

    protected $description = 'Cancel stale unpaid online and bank-transfer orders and release their stock';

    public function handle(OrderStateService $orderState): int
    {
        $now = now();
        $onlineCutoff = $now->copy()->subMinutes((int) config('order.expire_unpaid.online_minutes'));
        $bankCutoff = $now->copy()->subHours((int) config('order.expire_unpaid.bank_transfer_hours'));

        $expired = 0;

        Order::query()
            ->where('status', OrderConst::STATUS_PENDING)
            ->whereIn('payment_status', PaymentConst::payableStatuses())
            ->where(function ($query) use ($onlineCutoff, $bankCutoff) {
                $query->where(function ($q) use ($onlineCutoff) {
                    $q->whereIn('payment_method', [PaymentConst::METHOD_VNPAY, PaymentConst::METHOD_MOMO])
                        ->where('created_at', '<=', $onlineCutoff);
                })->orWhere(function ($q) use ($bankCutoff) {
                    $q->where('payment_method', PaymentConst::METHOD_BANK_TRANSFER)
                        ->where('created_at', '<=', $bankCutoff);
                });
            })
            ->orderBy('id')
            ->chunkById(OrderConst::EXPIRY_CHUNK, function ($orders) use ($orderState, &$expired) {
                foreach ($orders as $order) {
                    try {
                        $orderState->transition(
                            $order,
                            OrderConst::STATUS_CANCELLED,
                            OrderConst::ACTOR_SYSTEM,
                            null,
                            __('admin/order.messages.expired_unpaid')
                        );

                        $expired++;
                    } catch (OrderStateException $e) {
                        $this->warn($order->code . ': ' . $e->getMessage());
                    }
                }
            });

        $this->info("Expired {$expired} unpaid order(s).");

        return self::SUCCESS;
    }
}
