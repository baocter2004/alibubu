<?php

namespace App\Repositories;

use App\Models\CartItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CartItemRepository extends BaseRepository
{
    public function getModel(): CartItem
    {
        if (empty($this->model)) {
            $this->model = app()->make(CartItem::class);
        }

        return $this->model;
    }

    public function rawItemsForUser(string $userId): array
    {
        return $this->newQuery()
            ->where('user_id', $userId)
            ->get(['product_id', 'product_variant_id', 'quantity'])
            ->mapWithKeys(fn ($row) => [
                $row->product_id . '|' . ($row->product_variant_id ?? '') => [
                    'product_id' => $row->product_id,
                    'product_variant_id' => $row->product_variant_id,
                    'quantity' => $row->quantity,
                ],
            ])
            ->all();
    }

    public function replaceForUser(string $userId, array $items): void
    {
        DB::transaction(function () use ($userId, $items) {
            $this->newQuery()->where('user_id', $userId)->delete();

            if ($items === []) {
                return;
            }

            $now = now();

            $rows = collect($items)->map(fn (array $item) => [
                'id' => (string) Str::uuid(),
                'user_id' => $userId,
                'product_id' => $item['product_id'],
                'product_variant_id' => $item['product_variant_id'],
                'quantity' => $item['quantity'],
                'created_at' => $now,
                'updated_at' => $now,
            ])->all();

            $this->newQuery()->insert($rows);
        });
    }
}
