<?php

namespace App\Services\Client;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CompareService
{
    public const SESSION_KEY = 'compare';

    public const MAX_ITEMS = 4;

    public function ids(): array
    {
        return array_values(array_unique(session()->get(self::SESSION_KEY, [])));
    }

    public function count(): int
    {
        return count($this->ids());
    }

    public function has(int|string $id): bool
    {
        return in_array((string) $id, $this->ids(), true);
    }

    public function isFull(): bool
    {
        return $this->count() >= self::MAX_ITEMS;
    }

    public function add(Product $product): void
    {
        if ($this->has($product->id)) {
            return;
        }

        if ($this->isFull()) {
            throw new RuntimeException(__('client.compare.messages.full', ['max' => self::MAX_ITEMS]));
        }

        $categoryIds = $this->categoryIdsOf($product);

        if ($categoryIds === []) {
            throw new RuntimeException(__('client.compare.messages.no_category'));
        }

        $shared = $this->sharedCategoryIds();

        if ($shared !== [] && array_intersect($shared, $categoryIds) === []) {
            throw new RuntimeException(__('client.compare.messages.different_category'));
        }

        session()->put(self::SESSION_KEY, array_merge($this->ids(), [(string) $product->id]));
    }

    public function remove(int|string $id): void
    {
        $ids = array_values(array_diff($this->ids(), [(string) $id]));

        $ids === [] ? $this->clear() : session()->put(self::SESSION_KEY, $ids);
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    public function toggle(Product $product): bool
    {
        if ($this->has($product->id)) {
            $this->remove($product->id);

            return false;
        }

        $this->add($product);

        return true;
    }

    public function products(): Collection
    {
        $ids = $this->ids();

        if ($ids === []) {
            return collect();
        }

        $products = Product::query()
            ->whereIn('id', $ids)
            ->where('is_active', true)
            ->with([
                'branch',
                'categories',
                'tags',
                'specifications',
                'variants.attributeValues.attribute',
            ])
            ->get()
            ->keyBy('id');

        $ordered = collect($ids)->map(fn (string $id) => $products->get($id))->filter()->values();

        if ($ordered->count() !== count($ids)) {
            $remaining = $ordered->pluck('id')->map(fn ($id) => (string) $id)->all();

            $remaining === [] ? $this->clear() : session()->put(self::SESSION_KEY, $remaining);
        }

        return $ordered;
    }

    public function summary(): Collection
    {
        $ids = $this->ids();

        if ($ids === []) {
            return collect();
        }

        $products = Product::query()
            ->whereIn('id', $ids)
            ->where('is_active', true)
            ->get(['id', 'name', 'slug', 'thumbnail'])
            ->keyBy('id');

        return collect($ids)->map(fn (string $id) => $products->get($id))->filter()->values();
    }

    public function matrix(Collection $products): array
    {
        if ($products->isEmpty()) {
            return [];
        }

        $sections = [
            $this->section(__('client.compare.sections.pricing'), [
                $this->row(__('client.compare.fields.price'), $products, fn (Product $p) => [
                    'text' => $p->effective_price === null ? null : format_price($p->effective_price),
                    'raw' => $p->effective_price,
                ], 'min'),
                $this->row(__('client.compare.fields.base_price'), $products, fn (Product $p) => [
                    'text' => $p->base_price === null ? null : format_price($p->base_price),
                    'raw' => $p->base_price,
                ]),
                $this->row(__('client.compare.fields.discount'), $products, fn (Product $p) => [
                    'text' => $p->discount_percent > 0 ? '-' . $p->discount_percent . '%' : null,
                    'raw' => $p->discount_percent,
                ], 'max'),
            ]),
            $this->section(__('client.compare.sections.reputation'), [
                $this->row(__('client.compare.fields.rating'), $products, fn (Product $p) => [
                    'text' => number_format((float) $p->rating, 1) . '/5',
                    'raw' => (float) $p->rating,
                ], 'max'),
                $this->row(__('client.compare.fields.reviews'), $products, fn (Product $p) => [
                    'text' => number_format($p->reviews_count),
                    'raw' => $p->reviews_count,
                ], 'max'),
                $this->row(__('client.compare.fields.sold'), $products, fn (Product $p) => [
                    'text' => number_format($p->sold),
                    'raw' => $p->sold,
                ], 'max'),
                $this->row(__('client.compare.fields.views'), $products, fn (Product $p) => [
                    'text' => number_format($p->views),
                    'raw' => $p->views,
                ]),
            ]),
            $this->section(__('client.compare.sections.general'), [
                $this->row(__('client.compare.fields.brand'), $products, fn (Product $p) => [
                    'text' => $p->branch?->name,
                ]),
                $this->row(__('client.compare.fields.category'), $products, fn (Product $p) => [
                    'text' => $p->categories->pluck('name')->implode(', ') ?: null,
                ]),
                $this->row(__('client.compare.fields.tags'), $products, fn (Product $p) => [
                    'text' => $p->tags->pluck('name')->implode(', ') ?: null,
                ]),
                $this->row(__('client.compare.fields.sku'), $products, fn (Product $p) => [
                    'text' => $p->sku,
                ]),
                $this->row(__('client.compare.fields.stock'), $products, fn (Product $p) => [
                    'text' => $p->inStock()
                        ? __('client.compare.in_stock', ['count' => number_format($p->stock)])
                        : __('client.product.out_of_stock'),
                    'raw' => $p->stock,
                ], 'max'),
            ]),
        ];

        return array_values(array_filter(array_merge(
            $sections,
            [$this->variantSection($products)],
            $this->specificationSections($products)
        )));
    }

    protected function variantSection(Collection $products): ?array
    {
        $options = $products->mapWithKeys(function (Product $product) {
            $values = $product->variants
                ->where('is_active', true)
                ->flatMap(fn ($variant) => $variant->attributeValues)
                ->groupBy(fn ($value) => $value->attribute?->name ?: __('client.compare.fields.variant'))
                ->map(fn (Collection $group) => $group->pluck('value')->unique()->implode(', '));

            return [(string) $product->id => $values];
        });

        $names = $options->flatMap(fn (Collection $values) => $values->keys())->unique()->values();

        if ($names->isEmpty()) {
            return null;
        }

        return $this->section(
            __('client.compare.sections.variants'),
            $names->map(fn (string $name) => $this->row(
                $name,
                $products,
                fn (Product $product) => ['text' => $options[(string) $product->id][$name] ?? null]
            ))->all()
        );
    }

    protected function specificationSections(Collection $products): array
    {
        $byProduct = $products->mapWithKeys(fn (Product $product) => [
            (string) $product->id => $product->specifications
                ->groupBy(fn ($spec) => $spec->group ?: '')
                ->map(fn (Collection $specs) => $specs->pluck('value', 'name')),
        ]);

        $groups = $products
            ->flatMap(fn (Product $product) => $product->specifications)
            ->groupBy(fn ($spec) => $spec->group ?: '')
            ->map(fn (Collection $specs) => $specs->sortBy('ordinal')->pluck('name')->unique()->values());

        return $groups
            ->map(fn (Collection $names, string $group) => $this->section(
                $group !== '' ? $group : __('client.compare.sections.specifications'),
                $names->map(fn (string $name) => $this->row(
                    $name,
                    $products,
                    fn (Product $product) => ['text' => $byProduct[(string) $product->id][$group][$name] ?? null]
                ))->all()
            ))
            ->values()
            ->all();
    }

    protected function section(string $title, array $rows): ?array
    {
        $rows = array_values(array_filter($rows));

        return $rows === [] ? null : ['title' => $title, 'rows' => $rows];
    }

    protected function row(string $label, Collection $products, callable $resolver, ?string $prefer = null): ?array
    {
        $cells = $products->map(function (Product $product) use ($resolver) {
            $cell = $resolver($product);

            return [
                'text' => $cell['text'] ?? null,
                'raw' => $cell['raw'] ?? null,
            ];
        })->all();

        if (collect($cells)->every(fn (array $cell) => $cell['text'] === null || $cell['text'] === '')) {
            return null;
        }

        return [
            'label' => $label,
            'cells' => $cells,
            'best' => $this->bestIndex($cells, $prefer),
            'same' => collect($cells)->pluck('text')->unique()->count() === 1,
        ];
    }

    protected function bestIndex(array $cells, ?string $prefer): ?int
    {
        if ($prefer === null) {
            return null;
        }

        $values = collect($cells)
            ->map(fn (array $cell) => $cell['raw'])
            ->filter(fn ($value) => is_numeric($value) && $value > 0);

        if ($values->count() < 2 || $values->unique()->count() === 1) {
            return null;
        }

        $target = $prefer === 'min' ? $values->min() : $values->max();

        return (int) $values->search(fn ($value) => (float) $value === (float) $target);
    }

    protected function sharedCategoryIds(): array
    {
        $ids = $this->ids();

        if ($ids === []) {
            return [];
        }

        $grouped = DB::table('category_product')
            ->whereIn('product_id', $ids)
            ->get(['product_id', 'category_id'])
            ->groupBy('product_id')
            ->map(fn (Collection $rows) => $rows->pluck('category_id')->all())
            ->values()
            ->all();

        if ($grouped === []) {
            return [];
        }

        return array_values(array_intersect(...$grouped));
    }

    protected function categoryIdsOf(Product $product): array
    {
        return $product->relationLoaded('categories')
            ? $product->categories->pluck('id')->map(fn ($id) => (string) $id)->all()
            : DB::table('category_product')->where('product_id', $product->id)->pluck('category_id')->all();
    }
}
