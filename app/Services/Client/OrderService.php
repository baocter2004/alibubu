<?php

namespace App\Services\Client;

use App\Const\MembershipConst;
use App\Const\OrderConst;
use App\Const\PaymentConst;
use App\Const\PermissionConst;
use App\Mail\OrderPlaced;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Notifications\LowStock;
use App\Notifications\NewOrderPlaced;
use App\Services\Admin\AdminNotifierService;
use App\Services\Order\OrderInventoryService;
use App\Services\Order\OrderStateService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(
        protected CartService $cartService,
        protected CouponService $couponService,
        protected OrderInventoryService $inventory,
        protected OrderStateService $orderState
    ) {}

    public function place(array $params, int|string|null $userId = null): Order
    {
        $items = $this->cartService->items();

        if ($items->isEmpty()) {
            throw new \RuntimeException(__('client.messages.cart_empty'));
        }

        $subtotal = $this->cartService->subtotal($items);
        $user = $userId ? User::find($userId) : null;

        [$order, $alerts] = DB::transaction(function () use ($items, $params, $userId, $subtotal, $user) {
            $applied = $this->couponService->currentForOrder($items, $subtotal, $user, $params['phone_number'] ?? null);
            $coupon = $applied['coupon'] ?? null;
            $discount = (float) ($applied['discount'] ?? 0);

            $tier = $user?->membershipTier();
            $membershipDiscount = $tier ? MembershipConst::discountFor($tier, $subtotal) : 0.0;
            $total = max($subtotal - $discount - $membershipDiscount, 0);
            $isFree = $total <= 0;

            $order = Order::create(array_merge([
                'code' => $this->generateCode(),
                'user_id' => $userId,
                'fullname' => $params['fullname'],
                'phone_number' => $params['phone_number'],
                'email' => $params['email'] ?? null,
                'address' => $params['address'],
                'note' => $params['note'] ?? null,
                'locale' => App::getLocale(),
                'membership_tier' => $tier,
                'membership_discount' => $membershipDiscount,
                'total_amount' => $total,
                'status' => OrderConst::STATUS_PENDING,
                'payment_method' => (int) ($params['payment_method'] ?? PaymentConst::METHOD_COD),
                'payment_status' => $isFree ? PaymentConst::STATUS_PAID : PaymentConst::STATUS_UNPAID,
                'is_paid' => $isFree,
                'paid_at' => $isFree ? now() : null,
            ], $this->couponSnapshot($coupon, $discount)));

            $this->createItems($order, $items);
            $alerts = $this->inventory->consume($items);
            $this->consumeCoupon($coupon, $userId);

            $this->orderState->recordHistory($order, OrderConst::EVENT_PLACED, OrderConst::ACTOR_CUSTOMER, $userId ? (string) $userId : null);

            $this->cartService->clear();
            $this->couponService->forget();

            return [$order, $alerts];
        });

        $this->sendConfirmationMail($order, $user);
        $this->notifyAdmins($order);
        $this->notifyLowStock($alerts);

        return $order;
    }

    protected function notifyAdmins(Order $order): void
    {
        DB::afterCommit(function () use ($order) {
            try {
                AdminNotifierService::notify(new NewOrderPlaced($order), PermissionConst::ORDERS_VIEW);
            } catch (\Throwable $th) {
                Log::error(__METHOD__, [
                    'message' => $th->getMessage(),
                    'order_code' => $order->code,
                ]);
            }
        });
    }

    protected function notifyLowStock(array $alerts): void
    {
        if (empty($alerts)) {
            return;
        }

        DB::afterCommit(function () use ($alerts) {
            foreach ($alerts as $alert) {
                try {
                    AdminNotifierService::notify(new LowStock($alert), PermissionConst::PRODUCTS_VIEW);
                } catch (\Throwable $th) {
                    Log::error(__METHOD__, [
                        'message' => $th->getMessage(),
                        'product_id' => $alert['product_id'] ?? null,
                    ]);
                }
            }
        });
    }

    protected function sendConfirmationMail(Order $order, ?User $user = null): void
    {
        $email = $order->email ?: $user?->email;

        if (! $email) {
            return;
        }

        DB::afterCommit(function () use ($order, $email) {
            try {
                Mail::to($email)->queue(new OrderPlaced($order));
            } catch (\Throwable $th) {
                Log::error(__METHOD__, [
                    'message' => $th->getMessage(),
                    'order_code' => $order->code,
                ]);
            }
        });
    }

    protected function couponSnapshot(?Coupon $coupon, float $discount): array
    {
        if (! $coupon) {
            return [];
        }

        return [
            'coupon_id' => $coupon->id,
            'coupon_code' => $coupon->code,
            'coupon_description' => $coupon->description,
            'coupon_discount_type' => (string) $coupon->discount_type,
            'coupon_discount_value' => $discount,
            'max_discount_value' => $coupon->restriction?->max_discount_value,
        ];
    }

    protected function consumeCoupon(?Coupon $coupon, int|string|null $userId): void
    {
        if (! $coupon) {
            return;
        }

        $affected = Coupon::query()
            ->whereKey($coupon->id)
            ->where(function ($query) {
                $query->where('usage_limit', 0)
                    ->orWhereColumn('usage_count', '<', 'usage_limit');
            })
            ->increment('usage_count');

        if ($affected === 0) {
            throw new \RuntimeException(__('client.coupon.messages.exhausted'));
        }

        if ($userId) {
            $coupon->users()->attach($userId, [
                'id' => (string) Str::uuid(),
                'times_used' => 1,
                'used_at' => now(),
            ]);
        }
    }

    protected function createItems(Order $order, Collection $items): void
    {
        foreach ($items as $item) {
            $product = $item['product'];
            $variant = $item['variant'];

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'product_variant_id' => $variant?->id,
                'name' => $product->name,
                'price' => $item['price'],
                'old_price' => $product->base_price,
                'old_price_variant' => $variant?->price,
                'quantity' => $item['quantity'],
                'name_variant' => $variant?->sku,
                'attributes_variant' => $variant
                    ? $variant->attributeValues->pluck('value')->all()
                    : null,
                'price_variant' => $variant?->effective_price,
                'quantity_variant' => $variant ? $item['quantity'] : null,
            ]);
        }
    }

    protected function generateCode(): string
    {
        do {
            $code = 'ORD' . now()->format('ymd') . Str::upper(Str::random(6));
        } while (Order::where('code', $code)->exists());

        return $code;
    }
}
