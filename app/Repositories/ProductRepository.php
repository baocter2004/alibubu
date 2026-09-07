<?php

namespace App\Repositories;

use App\Const\ProductConst;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;

class ProductRepository extends BaseRepository
{
    protected const KEYWORD_COLUMNS = ['products.name', 'products.sku', 'products.short_descriptions'];

    protected const KEYWORD_ASCII_COLUMNS = ['products.slug'];

    protected const KEYWORD_RELATIONS = [
        'branch' => 'branches.name',
        'categories' => 'categories.name',
        'tags' => 'tags.name',
    ];

    protected const KEYWORD_ASCII_RELATIONS = [
        'branch' => 'branches.slug',
        'categories' => 'categories.slug',
        'tags' => 'tags.slug',
    ];

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

        $this->applyKeyword(
            $query,
            $params['keyword'] ?? null,
            self::KEYWORD_COLUMNS,
            self::KEYWORD_RELATIONS,
            self::KEYWORD_ASCII_COLUMNS,
            self::KEYWORD_ASCII_RELATIONS
        );
        $this->applyPriceRange($query, $params['price_from'] ?? null, $params['price_to'] ?? null);

        if (! empty($params['on_sale'])) {
            $this->applyOnSale($query);
        }

        $this->applySaleState($query, $params['sale_state'] ?? null);
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

    protected function applySaleState(Builder $query, ?string $state): void
    {
        if ($state === null) {
            return;
        }

        $now = now()->toDateTimeString();

        match ($state) {
            'active' => $query
                ->where('products.is_sale', true)
                ->whereNotNull('products.sale_price')
                ->where(fn (Builder $w) => $w->whereNull('products.sale_price_start_at')->orWhere('products.sale_price_start_at', '<=', $now))
                ->where(fn (Builder $w) => $w->whereNull('products.sale_price_end_at')->orWhere('products.sale_price_end_at', '>=', $now)),
            'scheduled' => $query
                ->where('products.is_sale', true)
                ->whereNotNull('products.sale_price_start_at')
                ->where('products.sale_price_start_at', '>', $now),
            'expired' => $query
                ->where('products.is_sale', true)
                ->whereNotNull('products.sale_price_end_at')
                ->where('products.sale_price_end_at', '<', $now),
            'none' => $query->where(fn (Builder $w) => $w->where('products.is_sale', false)->orWhereNull('products.sale_price')),
            default => $query,
        };
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
}
