<?php

namespace App\Services\Admin;

use App\Models\Tag;
use App\Repositories\TagRepository;
use App\Services\BaseCrudService;
use App\Traits\GeneratesSlug;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TagService extends BaseCrudService
{
    use GeneratesSlug;

    protected function getRepository(): TagRepository
    {
        if (empty($this->repository)) {
            $this->repository = app()->make(TagRepository::class);
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
        $sort = Arr::get($params, 'sort', 'created_at:desc');
        $relates = Arr::get($params, 'relates', []);
        $relatesCount = Arr::get($params, 'relates_count', []);

        if (! empty($params['name'])) {
            $whereLikes['name'] = $params['name'];
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
        $data = array_merge(['name' => null], $validated);
        $data['id'] = $id;
        $data['slug'] = $this->generateSlug($data['name'], 'tags', $id);

        return $data;
    }

    public function create(array $params = []): Tag
    {
        return parent::create(Arr::except($params, ['id']));
    }

    public function update(int|string $id, array $params = []): Tag
    {
        return parent::update($id, Arr::except($params, ['id']));
    }

    public function delete($id)
    {
        try {
            $tag = $this->find($id);

            if (! $tag) {
                return [
                    'status' => false,
                    'message' => __('admin/tag.messages.not_found'),
                ];
            }

            DB::transaction(function () use ($tag, $id) {
                $tag->products()->detach();
                parent::delete($id);
            });

            return [
                'status' => true,
                'message' => __('admin/tag.messages.deleted'),
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
        $tag = $this->getRepository()->findWithTrashed($id);

        if (! $tag) {
            return false;
        }

        return DB::transaction(function () use ($tag, $id) {
            $tag->products()->detach();

            return parent::forceDelete($id);
        });
    }
}
