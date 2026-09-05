<?php

namespace App\Repositories;

use App\Const\ProductConst;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;

class ProductRepository extends BaseRepository
{
    protected const KEYWORD_COLUMNS = ['products.name', 'products.sku', 'products.short_descriptions'];

    protected const KEYWORD_MAX_WORDS = 6;

    protected const MONEY_PLACEHOLDER = 'CAST(? AS DECIMAL(11, 2))';

    public function getModel(): Product
    {
        if (empty($this->model)) {
            $this->model = app()->make(Product::class);
        }

        return $this->model;
    }

    public function filter(array $params): Builder
    {
        $query = parent::filter($params);

        $this->applyKeyword($query, (string) ($params['keyword'] ?? ''));
        $this->applyPriceRange($query, $params['price_from'] ?? null, $params['price_to'] ?? null);

        if (! empty($params['on_sale'])) {
            $this->applyOnSale($query);
        }

        $this->applyPriceSort($query, $params['price_sort'] ?? null);

        return $query;
    }

    public function effectivePriceSql(): string
    {
        return '(CASE
            WHEN products.type = ' . ProductConst::VARIANT . ' THEN (
                SELECT MIN(COALESCE(pv.sale_price, pv.price))
                FROM product_variants pv
                WHERE pv.product_id = products.id AND pv.is_active = 1
            )
            WHEN products.is_sale = 1
                AND products.sale_price IS NOT NULL
                AND (products.sale_price_start_at IS NULL OR products.sale_price_start_at <= ?)
                AND (products.sale_price_end_at IS NULL OR products.sale_price_end_at >= ?)
                THEN products.sale_price
            ELSE products.price
        END)';
    }

    protected function applyKeyword(Builder $query, string $keyword): void
    {
        $words = preg_split('/\s+/u', trim($keyword), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        foreach (array_slice($words, 0, self::KEYWORD_MAX_WORDS) as $word) {
            $like = $this->likeValue($word);

            $query->where(function (Builder $group) use ($like) {
                foreach (self::KEYWORD_COLUMNS as $index => $column) {
                    $this->whereLike($group, $column, $like, $index > 0);
                }

                $group->orWhereHas('branch', fn (Builder $sub) => $this->whereLike($sub, 'branches.name', $like, false));
                $group->orWhereHas('categories', fn (Builder $sub) => $this->whereLike($sub, 'categories.name', $like, false));
                $group->orWhereHas('tags', fn (Builder $sub) => $this->whereLike($sub, 'tags.name', $like, false));
            });
        }
    }

    protected function applyPriceRange(Builder $query, mixed $from, mixed $to): void
    {
        $expression = $this->effectivePriceSql();

        if ($from !== null && $from !== '') {
            $query->whereRaw($expression . ' >= ' . self::MONEY_PLACEHOLDER, [...$this->saleWindowBindings(), (float) $from]);
        }

        if ($to !== null && $to !== '') {
            $query->whereRaw($expression . ' <= ' . self::MONEY_PLACEHOLDER, [...$this->saleWindowBindings(), (float) $to]);
        }
    }

    protected function applyOnSale(Builder $query): void
    {
        [$from, $until] = $this->saleWindowBindings();

        $query->where(function (Builder $group) use ($from, $until) {
            $group
                ->where(function (Builder $single) use ($from, $until) {
                    $single
                        ->where('products.type', ProductConst::SINGLE)
                        ->where('products.is_sale', true)
                        ->whereNotNull('products.sale_price')
                        ->whereColumn('products.sale_price', '<', 'products.price')
                        ->where(fn (Builder $window) => $window
                            ->whereNull('products.sale_price_start_at')
                            ->orWhere('products.sale_price_start_at', '<=', $from))
                        ->where(fn (Builder $window) => $window
                            ->whereNull('products.sale_price_end_at')
                            ->orWhere('products.sale_price_end_at', '>=', $until));
                })
                ->orWhere(function (Builder $variant) {
                    $variant
                        ->where('products.type', ProductConst::VARIANT)
                        ->whereHas('variants', fn (Builder $sub) => $sub
                            ->where('is_active', true)
                            ->whereNotNull('sale_price')
                            ->whereColumn('sale_price', '<', 'price'));
                });
        });
    }

    protected function applyPriceSort(Builder $query, ?string $direction): void
    {
        if (! in_array($direction, ['asc', 'desc'], true)) {
            return;
        }

        $query
            ->orderByRaw($this->effectivePriceSql() . ' ' . $direction, $this->saleWindowBindings())
            ->orderByDesc('products.id');
    }

    protected function saleWindowBindings(): array
    {
        $now = now()->toDateTimeString();

        return [$now, $now];
    }

    protected function whereLike(Builder $query, string $column, string $value, bool $or = true): Builder
    {
        $sql = $column . " LIKE ? ESCAPE '\\'";

        return $or ? $query->orWhereRaw($sql, [$value]) : $query->whereRaw($sql, [$value]);
    }

    protected function likeValue(string $value): string
    {
        return '%' . str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value) . '%';
    }
}
