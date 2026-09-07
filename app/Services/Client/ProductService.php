<?php

namespace App\Services\Client;

use App\Models\Product;
use App\Repositories\ProductRepository;
use App\Services\BaseCrudService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class ProductService extends BaseCrudService
{
    public const PER_PAGE = 12;

    public const SUGGEST_MIN_LENGTH = 2;

    protected function getRepository(): ProductRepository
    {
        if (empty($this->repository)) {
            $this->repository = app()->make(ProductRepository::class);
        }

        return $this->repository;
    }

    protected function buildFilterParams(array $params = []): array
    {
        $wheres = Arr::get($params, 'wheres', []);
        $whereIns = Arr::get($params, 'where_ins', []);
        $whereLikes = Arr::get($params, 'where_likes', []);
        $whereEquals = Arr::get($params, 'where_equals', []);
        $whereHas = Arr::get($params, 'where_has', []);
        $whereBetweens = Arr::get($params, 'where_betweens', []);
        $orWheres = Arr::get($params, 'or_wheres', []);
        $relates = Arr::get($params, 'relates', []);
        $relatesCount = Arr::get($params, 'relates_count', []);

        if (! Arr::has($params, 'wheres.is_active')) {
            $wheres['is_active'] = 1;
        }

        if (! empty($params['category_id'])) {
            $whereHas['categories'] = ['categories.id' => $params['category_id']];
        }

        if (! empty($params['branch_id'])) {
            $wheres['branch_id'] = $params['branch_id'];
        }

        if (! empty($params['tag_id'])) {
            $whereHas['tags'] = ['tags.id' => $params['tag_id']];
        }

        if (! empty($params['is_featured'])) {
            $wheres['is_featured'] = 1;
        }

        if (! empty($params['is_trending'])) {
            $wheres['is_trending'] = 1;
        }

        $sort = $params['sort'] ?? null;

        return [
            'wheres' => $wheres,
            'where_equals' => $whereEquals,
            'or_wheres' => $orWheres,
            'where_likes' => $whereLikes,
            'where_ins' => $whereIns,
            'where_has' => $whereHas,
            'where_betweens' => $whereBetweens,
            'sort' => $this->resolveSort($sort),
            'relates' => $relates,
            'relates_count' => $relatesCount,
            'keyword' => trim((string) ($params['keyword'] ?? '')),
            'price_from' => $params['min_price'] ?? null,
            'price_to' => $params['max_price'] ?? null,
            'on_sale' => ! empty($params['is_sale']),
            'price_sort' => $this->resolvePriceSort($sort),
        ];
    }

    public function searchForShop(array $params = [], int $limit = self::PER_PAGE): LengthAwarePaginator
    {
        return $this->paginate(
            array_merge($params, ['relates' => ['branch', 'variants']]),
            $limit
        );
    }

    public function suggest(string $keyword, int $limit = 6): Collection
    {
        if (mb_strlen(trim($keyword)) < self::SUGGEST_MIN_LENGTH) {
            return new Collection();
        }

        return $this->filter([
            'keyword' => $keyword,
            'sort' => 'popular',
            'relates' => ['variants'],
        ])->limit($limit)->get();
    }

    public function onSale(int $limit = 4): Collection
    {
        return $this->filter([
            'is_sale' => 1,
            'relates' => ['branch', 'variants'],
        ])->limit($limit)->get();
    }

    public function saleDeadline(): ?Carbon
    {
        $value = $this->filter(['is_sale' => 1])
            ->whereNotNull('products.sale_price_end_at')
            ->where('products.sale_price_end_at', '>', now())
            ->min('products.sale_price_end_at');

        return $value ? Carbon::parse($value) : null;
    }

    public const RECENT_KEY = 'recently_viewed';

    public const RECENT_LIMIT = 8;

    public function rememberViewed(Product $product): void
    {
        $ids = collect(session()->get(self::RECENT_KEY, []))
            ->reject(fn ($id) => (string) $id === (string) $product->id)
            ->prepend((string) $product->id)
            ->take(self::RECENT_LIMIT)
            ->values()
            ->all();

        session()->put(self::RECENT_KEY, $ids);
    }

    public function recentlyViewed(?string $exceptId = null, int $limit = 6): Collection
    {
        $ids = collect(session()->get(self::RECENT_KEY, []))
            ->reject(fn ($id) => $exceptId !== null && (string) $id === (string) $exceptId)
            ->take($limit)
            ->values();

        if ($ids->isEmpty()) {
            return new Collection();
        }

        $products = $this->filter(['relates' => ['branch', 'variants']])
            ->whereIn('products.id', $ids->all())
            ->get()
            ->keyBy('id');

        return new Collection($ids->map(fn ($id) => $products->get($id))->filter()->values()->all());
    }

    public function highlights(string $flag, int $limit = 8): Collection
    {
        return $this->filter([
            $flag => 1,
            'relates' => ['branch', 'variants'],
        ])->limit($limit)->get();
    }

    public function findBySlug(string $slug): ?Product
    {
        return $this->getRepository()
            ->newQuery()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->with([
                'branch',
                'categories',
                'tags',
                'galleries',
                'variants.attributeValues.attribute',
                'accessories' => fn ($query) => $query->where('is_active', true)->with(['branch', 'variants'])->limit(8),
            ])
            ->first();
    }

    public function findActive(int|string $id): ?Product
    {
        return $this->getRepository()
            ->newQuery()
            ->where('id', $id)
            ->where('is_active', true)
            ->with([
                'branch',
                'categories',
                'tags',
                'galleries',
                'variants.attributeValues.attribute',
                'accessories' => fn ($query) => $query->where('is_active', true)->with(['branch', 'variants'])->limit(8),
            ])
            ->first();
    }

    public function related(Product $product, int $limit = 4): Collection
    {
        $categoryIds = $product->categories->pluck('id')->all();

        return $this->getRepository()
            ->newQuery()
            ->where('is_active', true)
            ->whereKeyNot($product->id)
            ->when($categoryIds, fn ($query) => $query->whereHas(
                'categories',
                fn ($sub) => $sub->whereIn('categories.id', $categoryIds)
            ))
            ->with(['branch', 'variants'])
            ->latest('id')
            ->limit($limit)
            ->get();
    }

    protected function resolveSort(?string $sort): string
    {
        return match ($sort) {
            'price_asc', 'price_desc' => '',
            'popular' => 'sold:desc',
            'oldest' => 'id:asc',
            default => 'id:desc',
        };
    }

    protected function resolvePriceSort(?string $sort): ?string
    {
        return match ($sort) {
            'price_asc' => 'asc',
            'price_desc' => 'desc',
            default => null,
        };
    }
}
