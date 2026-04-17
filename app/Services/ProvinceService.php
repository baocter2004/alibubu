<?php

namespace App\Services;

use App\Repositories\ProvinceRepository;
use Illuminate\Support\Arr;

class ProvinceService extends BaseCrudService
{
    public function getRepository(): ProvinceRepository
    {
        if (empty($this->repository)) {
            $this->repository = app()->make(ProvinceRepository::class);
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

        if (! empty($params['division_type'])) {
            $whereLikes['division_type'] = $params['division_type'];
        }

        if (! empty($params['keyword'])) {
            $orWheres[] = ['name', 'like', '%'.$params['keyword'].'%'];
            $orWheres[] = ['codename', 'like', '%' . $params['keyword'].'%'];
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
