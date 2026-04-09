<?php

namespace App\Services;

use App\Services\BaseService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

abstract class BaseCrudService extends BaseService
{
    public function search(array $params = [], $limit = 10): LengthAwarePaginator
    {
        return $this->paginate($params, $limit);
    }

    public function all(array $params): Collection
    {
        return $this->get($params);
    }

    public function searchTrashed(array $params = [], $limit = 10): LengthAwarePaginator
    {
        return $this->getRepository()->getListTrashed($params, $limit);
    }
}
