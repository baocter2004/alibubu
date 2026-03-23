<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;

abstract class BaseRepository
{
    protected $model;

    public function __construct()
    {
        $this->setModel();
    }

    abstract public function getModel();

    public function setModel()
    {
        $this->model = app()->make($this->getModel());
    }

    protected function query(): Builder
    {
        return $this->model->newQuery();
    }

    public function getAll(
        array $columns = ['*'],
        array $relations = [],
    ) {
        return $this->query()
            ->select($columns)
            ->with($relations)
            ->get();
    }

    public function getAllActive(
        array $columns = ['*'],
        array $relations = [],
        int $isActive = 1,
    ) {
        return $this->query()
            ->select($columns)
            ->with($relations)
            ->where('is_active', $isActive)
            ->get();
    }

    public function findById(int $id, array $columns = ['*'])
    {
        return $this->query()
            ->select($columns)
            ->find($id);
    }

    public function create(array $data = [])
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data = [])
    {
        $model = $this->query()->find($id);

        if (!$model) {
            return false;
        }

        return $model->update($data);
    }

    public function delete(int $id)
    {
        $model = $this->query()->find($id);

        return $model ? $model->delete() : false;
    }

    public function forceDelete(int $id)
    {
        $model = $this->query()->find($id);

        return $model ? $model->forceDelete() : false;
    }

    // ================= FILTER =================

    public function filter(
        array $params = [],
        array $columns = ['*'],
        array $filters = [],
        array $relations = [],
        array $sorts = [],
        array $orderBy = ['id', 'DESC'],
    ): Builder {
        $query = $this->query()
            ->select($columns)
            ->with($relations);

        // FILTER
        foreach ($filters as $field => $operator) {
            $value = $params[$field] ?? null;

            if ($value === null || $value === '') {
                continue;
            }

            if (is_callable($operator)) {
                $operator($query, $value);
                continue;
            }

            switch (strtolower($operator)) {
                case 'like':
                    $query->where($field, 'LIKE', '%' . addcslashes($value, '%_') . '%');
                    break;

                case 'in':
                    if (is_array($value)) {
                        $query->whereIn($field, $value);
                    }
                    break;

                case 'between':
                    if (is_array($value) && count($value) === 2) {
                        $query->whereBetween($field, $value);
                    }
                    break;

                default:
                    $query->where($field, $operator, $value);
                    break;
            }
        }

        // SORT
        if (!empty($sorts)) {
            foreach ($sorts as $field => $direction) {
                $query->orderBy($field, $direction);
            }
        } else {
            $query->orderBy($orderBy[0], $orderBy[1]);
        }

        return $query;
    }

    // ================= EXECUTE =================

    public function paginateFilter(
        array $params = [],
        array $columns = ['*'],
        array $filters = [],
        array $relations = [],
        array $sorts = [],
        array $orderBy = ['id', 'DESC'],
        int $perPage = 15,
    ) {
        return $this->filter(
            $params,
            $columns,
            $filters,
            $relations,
            $sorts,
            $orderBy
        )->paginate($perPage)->withQueryString();
    }

    public function getFilter(...$args)
    {
        return $this->filter(...$args)->get();
    }

    public function firstFilter(...$args)
    {
        return $this->filter(...$args)->first();
    }
}
