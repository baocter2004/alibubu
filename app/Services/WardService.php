<?php

namespace App\Services;

use App\Repositories\WardRepository;
use App\Services\BaseCrudService;
use Illuminate\Support\Arr;

class WardService extends BaseCrudService
{
    public function getRepository(): WardRepository
    {
        if(empty($this->repository)) {
            $this->repository = app()->make(WardRepository::class);
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

        if (!empty($params['name'])) {
            $whereLikes['name'] = $params['name'];
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
}