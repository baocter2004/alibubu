<?php

namespace App\Services\Client;

use App\Models\Product;
use App\Repositories\ProductRepository;
use App\Services\BaseCrudService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

class ProductService extends BaseCrudService
{
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

        if (! empty($params['is_sale'])) {
            $wheres['is_sale'] = 1;
        }

        if (! empty($params['keyword'])) {
            $orWheres[] = ['name', 'like', '%' . $params['keyword'] . '%'];
            $orWheres[] = ['short_descriptions', 'like', '%' . $params['keyword'] . '%'];
            $orWheres[] = ['sku', 'like', '%' . $params['keyword'] . '%'];
        }

        $minPrice = $params['min_price'] ?? null;
        $maxPrice = $params['max_price'] ?? null;

        if ($minPrice !== null && $minPrice !== '' && $maxPrice !== null && $maxPrice !== '') {
            $whereBetweens['price'] = [(float) $minPrice, (float) $maxPrice];
        }

        return [
            'wheres' => $wheres,
            'where_equals' => $whereEquals,
            'or_wheres' => $orWheres,
            'where_likes' => $whereLikes,
            'where_ins' => $whereIns,
            'where_has' => $whereHas,
            'where_betweens' => $whereBetweens,
            'sort' => $this->resolveSort($params),
            'relates' => $relates,
            'relates_count' => $relatesCount,
        ];
    }

    public function searchForShop(array $params = [], int $limit = 12)
    {
        return $this->paginate(
            array_merge($params, ['relates' => ['branch', 'variants']]),
            $limit
        );
    }

    public function findBySlug(string $slug): ?Product
    {
        return $this->getRepository()
            ->newQuery()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->with(['branch', 'categories', 'tags', 'galleries', 'variants.attributeValues.attribute'])
            ->first();
    }

    public function findActive(int|string $id): ?Product
    {
        return $this->getRepository()
            ->newQuery()
            ->where('id', $id)
            ->where('is_active', true)
            ->with(['branch', 'categories', 'tags', 'galleries', 'variants.attributeValues.attribute'])
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

    public function highlights(string $flag, int $limit = 8): Collection
    {
        return $this->getRepository()
            ->newQuery()
            ->where('is_active', true)
            ->where($flag, true)
            ->with(['branch', 'variants'])
            ->latest('id')
            ->limit($limit)
            ->get();
    }

    protected function resolveSort(array $params): string
    {
        return match ($params['sort'] ?? null) {
            'price_asc' => 'price:asc',
            'price_desc' => 'price:desc',
            'popular' => 'views:desc',
            'oldest' => 'id:asc',
            default => 'id:desc',
        };
    }
}
