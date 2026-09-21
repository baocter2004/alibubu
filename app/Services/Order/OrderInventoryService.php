<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class OrderInventoryService
{
    public function consume(Collection $items): array
    {
        $threshold = (int) config('order.low_stock_threshold');
        $alerts = [];

        foreach ($items as $item) {
            $product = $item['product'];
            $variant = $item['variant'];
            $quantity = (int) $item['quantity'];

            if ($variant) {
                $variantAffected = ProductVariant::whereKey($variant->id)
                    ->where('stock', '>=', $quantity)
                    ->update(['stock' => DB::raw('stock - ' . $quantity)]);

                if ($variantAffected === 0) {
                    throw new RuntimeException(__('client.messages.out_of_stock', ['name' => $product->name]));
                }
            }

            $affected = Product::whereKey($product->id)
                ->where('stock', '>=', $quantity)
                ->update([
                    'stock' => DB::raw('stock - ' . $quantity),
                    'sold' => DB::raw('sold + ' . $quantity),
                ]);

            if ($affected === 0) {
                throw new RuntimeException(__('client.messages.out_of_stock', ['name' => $product->name]));
            }

            $remaining = $variant
                ? (int) ProductVariant::whereKey($variant->id)->value('stock')
                : (int) Product::whereKey($product->id)->value('stock');

            if ($remaining <= $threshold && $remaining + $quantity > $threshold) {
                $alerts[] = [
                    'product_id' => $product->id,
                    'product_variant_id' => $variant?->id,
                    'name' => $product->name,
                    'sku' => $variant?->sku ?? $product->sku,
                    'stock' => $remaining,
                ];
            }
        }

        return $alerts;
    }

    public function restore(Order $order): void
    {
        foreach ($order->items()->get() as $item) {
            $quantity = (int) $item->quantity;

            if (! $item->product_id || $quantity < 1) {
                continue;
            }

            if ($item->product_variant_id) {
                ProductVariant::whereKey($item->product_variant_id)->update([
                    'stock' => DB::raw('stock + ' . $quantity),
                ]);
            }

            Product::withTrashed()->whereKey($item->product_id)->update([
                'stock' => DB::raw('stock + ' . $quantity),
                'sold' => DB::raw('CASE WHEN sold > ' . $quantity . ' THEN sold - ' . $quantity . ' ELSE 0 END'),
            ]);
        }
    }
}
