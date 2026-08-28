<?php

namespace App\Services\Admin;

use App\Models\Attribute;
use App\Repositories\AttributeRepository;
use App\Services\BaseCrudService;
use App\Traits\GeneratesSlug;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AttributeService extends BaseCrudService
{
    use GeneratesSlug;

    protected function getRepository(): AttributeRepository
    {
        if (empty($this->repository)) {
            $this->repository = app()->make(AttributeRepository::class);
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
        $sort = Arr::get($params, 'sort', 'id:desc');
        $relates = Arr::get($params, 'relates', []);
        $relatesCount = Arr::get($params, 'relates_count', []);

        if (! empty($params['name'])) {
            $whereLikes['name'] = $params['name'];
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
        $data = array_merge([
            'name' => null,
            'slug' => null,
            'is_active' => 1,
            'values' => [],
        ], $validated);

        $data['id'] = $id;
        $data['slug'] = $this->generateSlug($data['name'], 'attributes', $id);
        $data['values'] = array_values(array_map(fn ($v) => array_merge([
            'id' => null,
            'value' => null,
            'is_active' => false,
        ], $v), $data['values'] ?? []));

        return $data;
    }

    public function create(array $params = []): Attribute
    {
        return DB::transaction(function () use ($params) {
            $attribute = parent::create(Arr::except($params, ['id', 'values']));
            $this->syncValues($attribute, $params['values'] ?? []);

            return $attribute;
        });
    }

    public function update(int|string $id, array $params = []): Attribute
    {
        return DB::transaction(function () use ($id, $params) {
            $attribute = parent::update($id, Arr::except($params, ['id', 'values']));
            $this->syncValues($attribute, $params['values'] ?? []);

            return $attribute->refresh();
        });
    }

    public function delete($id)
    {
        try {
            $attribute = $this->find($id);

            if (! $attribute) {
                return [
                    'status' => false,
                    'message' => __('admin/attribute.messages.not_found'),
                ];
            }

            if ($this->valuesInUse($attribute)) {
                return [
                    'status' => false,
                    'message' => __('admin/attribute.messages.in_use'),
                ];
            }

            DB::transaction(function () use ($attribute, $id) {
                $attribute->values()->delete();
                parent::delete($id);
            });

            return [
                'status' => true,
                'message' => __('admin/attribute.messages.deleted'),
            ];
        } catch (\Throwable $th) {
            Log::error(__METHOD__, ['message' => $th->getMessage(), 'id' => $id]);

            throw $th;
        }
    }

    public function restore($id)
    {
        return $this->getRepository()->restore($id);
    }

    public function forceDelete($id)
    {
        $attribute = $this->getRepository()->findWithTrashed($id);

        if (! $attribute) {
            return false;
        }

        return DB::transaction(function () use ($attribute, $id) {
            $attribute->values()->withTrashed()->forceDelete();

            return parent::forceDelete($id);
        });
    }

    protected function valuesInUse(Attribute $attribute): bool
    {
        return DB::table('attribute_value_product_variant')
            ->whereIn('attribute_value_id', $attribute->values()->pluck('id'))
            ->exists();
    }

    protected function syncValues(Attribute $attribute, array $values): void
    {
        $keptIds = [];

        foreach ($values as $value) {
            if (empty($value['value'])) {
                continue;
            }

            $model = ! empty($value['id'])
                ? $attribute->values()->whereKey($value['id'])->first()
                : null;

            $attributes = [
                'value' => $value['value'],
                'is_active' => ! empty($value['is_active']),
            ];

            if ($model) {
                $model->update($attributes);
            } else {
                $model = $attribute->values()->create($attributes);
            }

            $keptIds[] = $model->id;
        }

        $removable = $attribute->values()->whereNotIn('id', $keptIds)->pluck('id');

        if ($removable->isEmpty()) {
            return;
        }

        $used = DB::table('attribute_value_product_variant')
            ->whereIn('attribute_value_id', $removable)
            ->pluck('attribute_value_id')
            ->unique();

        $attribute->values()
            ->whereIn('id', $removable->diff($used))
            ->delete();
    }
}
