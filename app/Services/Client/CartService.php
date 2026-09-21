<?php

namespace App\Services\Client;

use App\Const\CartConst;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Repositories\CartItemRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class CartService
{
    public const SESSION_KEY = CartConst::SESSION_KEY;

    public const MAX_QUANTITY = CartConst::MAX_QUANTITY;

    protected ?Collection $itemsCache = null;

    protected ?array $rawItemsCache = null;

    public function __construct(protected CartItemRepository $cartItemRepository) {}

    public function add(Product $product, ?ProductVariant $variant, int $quantity = 1): bool
    {
        $items = $this->rawItems();
        $key = $this->makeKey((string) $product->id, $variant?->id ? (string) $variant->id : null);
        $current = $items[$key]['quantity'] ?? 0;
        $stock = $variant?->stock ?? $product->stock;
        $quantity = $this->clamp($current + $quantity, $stock);

        if ($quantity < 1) {
            unset($items[$key]);
            $this->persist($items);

            return true;
        }

        if (! isset($items[$key]) && count($items) >= CartConst::MAX_LINES) {
            return false;
        }

        $items[$key] = [
            'product_id' => $product->id,
            'product_variant_id' => $variant?->id,
            'quantity' => $quantity,
        ];

        $this->persist($items);

        return true;
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
        if ($userId = Auth::guard('user')->id()) {
            $this->cartItemRepository->replaceForUser((string) $userId, []);
        } else {
            session()->forget(self::SESSION_KEY);
        }

        $this->itemsCache = null;
        $this->rawItemsCache = [];
    }

    public function mergeGuestCartIntoUser(): void
    {
        $guestItems = session()->get(self::SESSION_KEY, []);

        if ($guestItems === [] || ! Auth::guard('user')->check()) {
            return;
        }

        $items = $this->rawItems();

        $products = Product::query()
            ->with('variants')
            ->whereIn('id', collect($guestItems)->pluck('product_id')->unique())
            ->get()
            ->keyBy('id');

        foreach ($guestItems as $key => $line) {
            $product = $products->get($line['product_id']);

            if (! $product) {
                continue;
            }

            $variant = $line['product_variant_id']
                ? $product->variants->firstWhere('id', $line['product_variant_id'])
                : null;

            $stock = $variant?->stock ?? $product->stock;
            $current = $items[$key]['quantity'] ?? 0;
            $quantity = $this->clamp($current + $line['quantity'], $stock);

            if ($quantity < 1) {
                continue;
            }

            $items[$key] = [
                'product_id' => $product->id,
                'product_variant_id' => $variant?->id,
                'quantity' => $quantity,
            ];
        }

        $this->persist($items);
        session()->forget(self::SESSION_KEY);
    }

    public function items(): Collection
    {
        if ($this->itemsCache !== null) {
            return $this->itemsCache;
        }

        return $this->itemsCache = $this->loadItems();
    }

    protected function loadItems(): Collection
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
        if ($this->rawItemsCache !== null) {
            return $this->rawItemsCache;
        }

        if ($userId = Auth::guard('user')->id()) {
            return $this->rawItemsCache = $this->cartItemRepository->rawItemsForUser((string) $userId);
        }

        return $this->rawItemsCache = session()->get(self::SESSION_KEY, []);
    }

    protected function persist(array $items): void
    {
        $this->itemsCache = null;

        if ($items === []) {
            $this->clear();

            return;
        }

        $this->rawItemsCache = $items;

        if ($userId = Auth::guard('user')->id()) {
            $this->cartItemRepository->replaceForUser((string) $userId, $items);

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
