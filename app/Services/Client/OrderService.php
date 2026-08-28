<?php

namespace App\Services\Client;

use App\Const\OrderConst;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(
        protected CartService $cartService,
        protected CouponService $couponService
    ) {}

    public function place(array $params, ?int $userId = null): Order
    {
        $items = $this->cartService->items();

        if ($items->isEmpty()) {
            throw new \RuntimeException(__('client.messages.cart_empty'));
        }

        $subtotal = $this->cartService->subtotal($items);
        $applied = $this->couponService->current($items, $subtotal, $userId ? \App\Models\User::find($userId) : null);
        $coupon = $applied['coupon'] ?? null;
        $discount = (float) ($applied['discount'] ?? 0);

        return DB::transaction(function () use ($items, $params, $userId, $subtotal, $coupon, $discount) {
            $order = Order::create(array_merge([
                'code' => $this->generateCode(),
                'user_id' => $userId,
                'fullname' => $params['fullname'],
                'phone_number' => $params['phone_number'],
                'email' => $params['email'] ?? null,
                'address' => $params['address'],
                'note' => $params['note'] ?? null,
                'total_amount' => max($subtotal - $discount, 0),
                'status' => OrderConst::STATUS_PENDING,
                'is_paid' => false,
            ], $this->couponSnapshot($coupon, $discount)));

            $this->createItems($order, $items);
            $this->consumeStock($items);
            $this->consumeCoupon($coupon, $userId);

            $this->cartService->clear();
            $this->couponService->forget();

            return $order;
        });
    }

    protected function consumeStock(Collection $items): void
    {
        foreach ($items as $item) {
            $product = $item['product'];
            $quantity = (int) $item['quantity'];

            $affected = \App\Models\Product::whereKey($product->id)
                ->where('stock', '>=', $quantity)
                ->update([
                    'stock' => DB::raw('stock - ' . $quantity),
                    'sold' => DB::raw('sold + ' . $quantity),
                ]);

            if ($affected === 0) {
                throw new \RuntimeException(__('client.messages.out_of_stock', ['name' => $product->name]));
            }
        }
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

    protected function consumeCoupon(?Coupon $coupon, ?int $userId): void
    {
        if (! $coupon) {
            return;
        }

        $coupon->increment('usage_count');

        if ($userId) {
            $coupon->users()->attach($userId, [
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
