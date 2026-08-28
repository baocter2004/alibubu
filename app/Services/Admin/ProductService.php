<?php

namespace App\Services\Admin;

use App\Const\GlobalConst;
use App\Const\ProductConst;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Repositories\ProductRepository;
use App\Services\BaseCrudService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
        $orWheres = Arr::get($params, 'or_wheres', []);
        $sort = Arr::get($params, 'sort', 'id:desc');
        $relates = Arr::get($params, 'relates', []);
        $relatesCount = Arr::get($params, 'relates_count', []);

        if (! empty($params['name'])) {
            $whereLikes['name'] = $params['name'];
        }

        if (! empty($params['sku'])) {
            $whereLikes['sku'] = $params['sku'];
        }

        if (! empty($params['branch_id'])) {
            $wheres['branch_id'] = $params['branch_id'];
        }

        if (! empty($params['category_id'])) {
            $whereHas['categories'] = ['categories.id' => $params['category_id']];
        }

        if (isset($params['is_active']) && $params['is_active'] !== '' && $params['is_active'] !== null) {
            $wheres['is_active'] = (int) $params['is_active'];
        }

        if (! empty($params['keyword'])) {
            $orWheres[] = ['name', 'like', '%' . $params['keyword'] . '%'];
            $orWheres[] = ['sku', 'like', '%' . $params['keyword'] . '%'];
            $orWheres[] = ['short_descriptions', 'like', '%' . $params['keyword'] . '%'];
        }

        return [
            'wheres' => $wheres,
            'where_equals' => $whereEquals,
            'or_wheres' => $orWheres,
            'where_likes' => $whereLikes,
            'where_ins' => $whereIns,
            'where_has' => $whereHas,
            'sort' => $sort,
            'relates' => $relates,
            'relates_count' => $relatesCount,
        ];
    }

    public function prepareConfirmData(array $validated, $id = null, ?array $oldSessionData = null): array
    {
        $data = array_merge([
            'name' => null,
            'slug' => null,
            'sku' => null,
            'branch_id' => null,
            'category_ids' => [],
            'short_descriptions' => null,
            'descriptions' => null,
            'thumbnail' => null,
            'type' => ProductConst::SINGLE,
            'price' => null,
            'sale_price' => null,
            'sale_price_start_at' => null,
            'sale_price_end_at' => null,
            'is_featured' => false,
            'is_trending' => false,
            'is_active' => GlobalConst::IS_ACTIVE,
            'variants' => [],
        ], $validated);

        $data['id'] = $id;
        $data['slug'] = ! empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['name']);
        $data['category_ids'] = array_values(array_filter($validated['category_ids'] ?? []));
        $data['variants'] = (int) $data['type'] === ProductConst::VARIANT
            ? $this->normalizeVariants($data['variants'])
            : [];

        if (! empty($validated['thumbnail']) && $validated['thumbnail'] instanceof UploadedFile) {
            if (! empty($oldSessionData['thumbnail']) && $oldSessionData['thumbnail'] !== ($oldSessionData['persisted_thumbnail'] ?? null)) {
                Storage::disk('public')->delete($oldSessionData['thumbnail']);
            }

            $data['thumbnail'] = $validated['thumbnail']->store('products', 'public');
        } elseif (! empty($oldSessionData['thumbnail'])) {
            $data['thumbnail'] = $oldSessionData['thumbnail'];
        }

        if (! empty($id)) {
            $product = $this->find($id);
            $data['persisted_thumbnail'] = $product?->thumbnail;

            if (empty($data['thumbnail'])) {
                $data['thumbnail'] = $product?->thumbnail;
            }
        }

        return $data;
    }

    protected function normalizeVariants(array $variants): array
    {
        return array_values(array_map(fn ($variant) => array_merge([
            'id' => null,
            'sku' => null,
            'price' => null,
            'sale_price' => null,
            'is_active' => false,
            'attribute_value_ids' => [],
        ], $variant), $variants));
    }

    public function create(array $params = []): Product
    {
        $thumbnail = Arr::get($params, 'thumbnail');

        try {
            return DB::transaction(function () use ($params) {
                $product = parent::create($this->productAttributes($params));
                $product->categories()->sync($params['category_ids'] ?? []);
                $this->syncVariants($product, $params['variants'] ?? []);

                return $product;
            });
        } catch (\Throwable $th) {
            if ($thumbnail) {
                Storage::disk('public')->delete($thumbnail);
            }

            Log::error(__METHOD__, ['message' => $th->getMessage(), 'params' => $params]);

            throw $th;
        }
    }

    public function update(int|string $id, array $params = []): Product
    {
        $newThumbnail = Arr::get($params, 'thumbnail');
        $oldThumbnail = Arr::get($params, 'persisted_thumbnail');

        try {
            $product = DB::transaction(function () use ($id, $params) {
                $product = parent::update($id, $this->productAttributes($params));
                $product->categories()->sync($params['category_ids'] ?? []);
                $this->syncVariants($product, $params['variants'] ?? []);

                return $product;
            });

            if ($newThumbnail && $oldThumbnail && $oldThumbnail !== $newThumbnail) {
                Storage::disk('public')->delete($oldThumbnail);
            }

            return $product;
        } catch (\Throwable $th) {
            if ($newThumbnail && $newThumbnail !== $oldThumbnail) {
                Storage::disk('public')->delete($newThumbnail);
            }

            Log::error(__METHOD__, ['message' => $th->getMessage(), 'id' => $id, 'params' => $params]);

            throw $th;
        }
    }

    public function delete($id)
    {
        try {
            $product = $this->find($id);

            if (! $product) {
                return [
                    'status' => false,
                    'message' => __('admin/product.messages.not_found'),
                ];
            }

            DB::transaction(function () use ($product, $id) {
                $product->update(['is_active' => false]);
                parent::delete($id);
            });

            return [
                'status' => true,
                'message' => __('admin/product.messages.deleted'),
            ];
        } catch (\Throwable $th) {
            Log::error(__METHOD__, ['message' => $th->getMessage(), 'id' => $id]);

            throw $th;
        }
    }

    public function restore($id)
    {
        $restored = $this->getRepository()->restore($id);
        $this->find($id)?->update(['is_active' => true]);

        return $restored;
    }

    public function forceDelete($id)
    {
        $product = $this->getRepository()->findWithTrashed($id);

        if (! $product) {
            return false;
        }

        $thumbnail = $product->thumbnail;

        $result = DB::transaction(function () use ($product, $id) {
            $product->categories()->detach();
            $product->tags()->detach();
            $product->galleries()->delete();
            $product->variants()->delete();

            return parent::forceDelete($id);
        });

        if ($result && $thumbnail) {
            Storage::disk('public')->delete($thumbnail);
        }

        return $result;
    }

    protected function syncVariants(Product $product, array $variants): void
    {
        if ($product->type !== ProductConst::VARIANT) {
            $product->variants()->each(function ($variant) {
                $variant->attributeValues()->detach();
                $variant->delete();
            });

            return;
        }

        $keptIds = [];

        foreach ($variants as $index => $variant) {
            $model = ! empty($variant['id'])
                ? $product->variants()->whereKey($variant['id'])->first()
                : null;

            $attributes = [
                'sku' => $variant['sku'] ?: ($model?->sku ?: $this->generateVariantSku($product, $index)),
                'price' => $variant['price'],
                'sale_price' => $variant['sale_price'] ?? null,
                'is_active' => ! empty($variant['is_active']),
            ];

            if ($model) {
                $model->update($attributes);
            } else {
                $model = ProductVariant::create(array_merge($attributes, [
                    'product_id' => $product->id,
                    'thumbnail' => $product->thumbnail,
                ]));
            }

            $model->attributeValues()->sync($variant['attribute_value_ids'] ?? []);
            $keptIds[] = $model->id;
        }

        $product->variants()
            ->whereNotIn('id', $keptIds)
            ->get()
            ->each(function ($variant) {
                $variant->attributeValues()->detach();
                $variant->delete();
            });
    }

    protected function generateVariantSku(Product $product, int $index): string
    {
        $base = $product->sku ?: Str::upper(Str::substr($product->slug, 0, 12));

        do {
            $sku = $base . '-V' . ($index + 1) . '-' . Str::upper(Str::random(3));
        } while (ProductVariant::where('sku', $sku)->exists());

        return $sku;
    }

    protected function productAttributes(array $params): array
    {
        $attributes = Arr::except($params, ['id', 'category_ids', 'persisted_thumbnail', 'variants']);
        $attributes['type'] = (int) ($params['type'] ?? ProductConst::SINGLE);

        if ($attributes['type'] === ProductConst::VARIANT) {
            $prices = collect($params['variants'] ?? [])->pluck('price')->filter()->map(fn ($p) => (float) $p);
            $salePrices = collect($params['variants'] ?? [])->pluck('sale_price')->filter()->map(fn ($p) => (float) $p);

            $attributes['price'] = $prices->min();
            $attributes['sale_price'] = $salePrices->count() === $prices->count() ? $salePrices->min() : null;
        }

        $attributes['is_sale'] = ! empty($attributes['sale_price']);

        foreach (['is_featured', 'is_trending', 'is_active'] as $flag) {
            $attributes[$flag] = ! empty($attributes[$flag]);
        }

        return $attributes;
    }
}
