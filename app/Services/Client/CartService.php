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
        $key = $this->makeKey((string) $product->id, $variant?->id ? (string) $variant->id : null);
        $current = $items[$key]['quantity'] ?? 0;
        $stock = $variant?->stock ?? $product->stock;
        $quantity = $this->clamp($current + $quantity, $stock);

        if ($quantity < 1) {
            unset($items[$key]);
            $this->persist($items);

            return;
        }

        $items[$key] = [
            'product_id' => $product->id,
            'product_variant_id' => $variant?->id,
            'quantity' => $quantity,
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
            $product = Product::query()
                ->with('variants')
                ->whereKey($items[$key]['product_id'])
                ->first();
            $variant = $product?->variants->firstWhere('id', $items[$key]['product_variant_id']);
            $stock = $variant?->stock ?? ($product?->stock);

            if (! $product || ! $product->is_active || ($items[$key]['product_variant_id'] && ! $variant) || ($variant && ! $variant->is_active) || $stock === null || (int) $stock < 1) {
                unset($items[$key]);
            } else {
                $items[$key]['quantity'] = $this->clamp($quantity, (int) $stock);
            }
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
            ->with(['variants', 'categories'])
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

                if ($item['product_variant_id'] && ! $variant->is_active) {
                    return null;
                }

                if ($product->hasVariants() && ! $variant) {
                    return null;
                }

                $stock = $variant?->stock ?? $product->stock;
                if ((int) $stock < 1) {
                    return null;
                }

                $price = $variant ? $variant->effective_price : (float) $product->effective_price;
                $quantity = $this->clamp($item['quantity'], $stock);
                if ($quantity < 1) {
                    return null;
                }

                return [
                    'key' => $key,
                    'product' => $product,
                    'variant' => $variant,
                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => $price * $quantity,
                ];
            })
            ->filter()
            ->values();
    }

    public function count(): int
    {
        return (int) $this->items()->sum('quantity');
    }

    public function subtotal(?Collection $items = null): float
    {
        return (float) ($items ?? $this->items())->sum('subtotal');
    }

    public function isEmpty(): bool
    {
        return $this->items()->isEmpty();
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

    protected function makeKey(string $productId, ?string $variantId): string
    {
        return $productId . '|' . ($variantId ?? '');
    }

    protected function clamp(int $quantity, ?int $stock = null): int
    {
        $ceiling = self::MAX_QUANTITY;

        if ($stock !== null) {
            $ceiling = min($ceiling, max(0, $stock));
        }

        return $ceiling < 1 ? 0 : max(1, min($quantity, $ceiling));
    }
}
