<?php

namespace App\Services\Admin;

use App\Models\Category;
use App\Repositories\CategoryRepository;
use App\Services\BaseCrudService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CategoryService extends BaseCrudService
{
    protected function getRepository(): CategoryRepository
    {
        if (empty($this->repository)) {
            $this->repository = app()->make(CategoryRepository::class);
        }

        return $this->repository;
    }

    protected function buildFilterParams(array $params = []): array
    {
        $wheres = Arr::get($params, 'wheres', []);
        $whereIns = Arr::get($params, 'where_ins', []);
        $whereLikes = Arr::get($params, 'where_likes', []);
        $whereEquals = Arr::get($params, 'where_equals', []);
        $orWheres = Arr::get($params, 'or_wheres', []);
        $sort = Arr::get($params, 'sort', 'ordinal:asc');
        $relates = Arr::get($params, 'relates', []);
        $relatesCount = Arr::get($params, 'relates_count', []);

        if (! empty($params['name'])) {
            $whereLikes['name'] = $params['name'];
        }

        if (! empty($params['parent_id'])) {
            $wheres['parent_id'] = $params['parent_id'];
        }

        if (isset($params['is_active']) && $params['is_active'] !== '' && $params['is_active'] !== null) {
            $wheres['is_active'] = (int) $params['is_active'];
        }

        if (! empty($params['keyword'])) {
            $orWheres[] = ['name', 'like', '%' . $params['keyword'] . '%'];
            $orWheres[] = ['slug', 'like', '%' . $params['keyword'] . '%'];
        }

        return [
            'wheres' => $wheres,
            'where_equals' => $whereEquals,
            'or_wheres' => $orWheres,
            'where_likes' => $whereLikes,
            'where_ins' => $whereIns,
            'sort' => $sort,
            'relates' => $relates,
            'relates_count' => $relatesCount,
        ];
    }

    public function prepareConfirmData(array $validated, $id = null): array
    {
        $data = $validated;
        $data['id'] = $id;
        $data['slug'] = ! empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['name']);

        return $data;
    }

    public function create(array $params = []): Category
    {
        return parent::create(Arr::except($params, ['id']));
    }

    public function update(int|string $id, array $params = []): Category
    {
        return parent::update($id, Arr::except($params, ['id']));
    }

    public function delete($id)
    {
        try {
            $category = $this->find($id);

            if (! $category) {
                return [
                    'status' => false,
                    'message' => __('admin/category.messages.not_found'),
                ];
            }

            if ($category->products()->exists()) {
                return [
                    'status' => false,
                    'message' => __('admin/category.messages.has_products'),
                ];
            }

            if ($category->children()->exists()) {
                return [
                    'status' => false,
                    'message' => __('admin/category.messages.has_children'),
                ];
            }

            DB::transaction(function () use ($category, $id) {
                $category->update(['is_active' => false]);
                parent::delete($id);
            });

            return [
                'status' => true,
                'message' => __('admin/category.messages.deleted'),
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
        $category = $this->getRepository()->findWithTrashed($id);

        if (! $category) {
            return false;
        }

        return DB::transaction(function () use ($category, $id) {
            $category->products()->detach();

            return parent::forceDelete($id);
        });
    }

    public function selectableParents(int|string|null $excludeId = null): \Illuminate\Support\Collection
    {
        return $this->getRepository()
            ->newQuery()
            ->when($excludeId, fn ($query) => $query->whereKeyNot($excludeId))
            ->orderBy('name')
            ->pluck('name', 'id');
    }
}
