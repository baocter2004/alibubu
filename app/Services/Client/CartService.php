<?php

namespace App\Services\Client;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;

class CartService
{
    public const SESSION_KEY = 'cart';

    public const MAX_QUANTITY = 20;

    public function add(Product $product, ?ProductVariant $variant, int $quantity = 1): void
    {
        $items = $this->rawItems();
        $key = $this->makeKey($product->id, $variant?->id);
        $current = $items[$key]['quantity'] ?? 0;

        $items[$key] = [
            'product_id' => $product->id,
            'product_variant_id' => $variant?->id,
            'quantity' => $this->clamp($current + $quantity),
        ];

        $this->persist($items);
    }

    public function update(string $key, int $quantity): void
    {
        $items = $this->rawItems();

        if (! isset($items[$key])) {
            return;
        }

        if ($quantity < 1) {
            unset($items[$key]);
        } else {
            $items[$key]['quantity'] = $this->clamp($quantity);
        }

        $this->persist($items);
    }

    public function remove(string $key): void
    {
        $items = $this->rawItems();
        unset($items[$key]);

        $this->persist($items);
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    public function items(): Collection
    {
        $raw = collect($this->rawItems());

        if ($raw->isEmpty()) {
            return collect();
        }

        $products = Product::query()
            ->with('variants')
            ->whereIn('id', $raw->pluck('product_id')->unique())
            ->get()
            ->keyBy('id');

        return $raw
            ->map(function (array $item, string $key) use ($products) {
                $product = $products->get($item['product_id']);

                if (! $product || ! $product->is_active) {
                    return null;
                }

                $variant = $item['product_variant_id']
                    ? $product->variants->firstWhere('id', $item['product_variant_id'])
                    : null;

                if ($item['product_variant_id'] && ! $variant) {
                    return null;
                }

                $price = $variant ? $variant->effective_price : (float) $product->effective_price;

                return [
                    'key' => $key,
                    'product' => $product,
                    'variant' => $variant,
                    'quantity' => $item['quantity'],
                    'price' => $price,
                    'subtotal' => $price * $item['quantity'],
                ];
            })
            ->filter()
            ->values();
    }

    public function count(): int
    {
        return (int) collect($this->rawItems())->sum('quantity');
    }

    public function subtotal(?Collection $items = null): float
    {
        return (float) ($items ?? $this->items())->sum('subtotal');
    }

    public function isEmpty(): bool
    {
        return $this->rawItems() === [];
    }

    protected function rawItems(): array
    {
        return session()->get(self::SESSION_KEY, []);
    }

    protected function persist(array $items): void
    {
        if ($items === []) {
            $this->clear();

            return;
        }

        session()->put(self::SESSION_KEY, $items);
    }

    protected function makeKey(int $productId, ?int $variantId): string
    {
        return $productId . ':' . ($variantId ?? 0);
    }

    protected function clamp(int $quantity): int
    {
        return max(1, min($quantity, self::MAX_QUANTITY));
    }
}
