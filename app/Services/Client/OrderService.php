<?php

namespace App\Services\Client;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(protected CartService $cartService) {}

    public function place(array $params, ?int $userId = null): Order
    {
        $items = $this->cartService->items();

        if ($items->isEmpty()) {
            throw new \RuntimeException(__('client.messages.cart_empty'));
        }

        return DB::transaction(function () use ($items, $params, $userId) {
            $order = Order::create([
                'code' => $this->generateCode(),
                'user_id' => $userId,
                'fullname' => $params['fullname'],
                'phone_number' => $params['phone_number'],
                'email' => $params['email'] ?? null,
                'address' => $params['address'],
                'note' => $params['note'] ?? null,
                'total_amount' => $this->cartService->subtotal($items),
                'is_paid' => false,
            ]);

            $this->createItems($order, $items);
            $this->cartService->clear();

            return $order;
        });
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
