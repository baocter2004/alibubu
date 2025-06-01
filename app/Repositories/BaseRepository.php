<?php

namespace App\Repositories;

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

        return $this->model = app()->make($this->getModel());
    }

    public function pagination(
        array $columns = ['*'],
        int $perPage = 15,
        array $orderBy = ['id', 'DESC'],
        array $relations = [],
    ) {
        return $this->model->select($columns)
            ->with($relations)
            ->orderBy($orderBy[0], $orderBy[1])
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getAll(
        array $columns = ['*'],
        array $relations = [],
    ) {
        return $this->model->select($columns)->with($relations)->get();
    }

    public function getAllActive(
        array $columns = ['*'],
        array $relations = [],
        int $isActive = 1,
    ) {
        return $this->model->select($columns)->with($relations)->where('is_active', $isActive)->get();
    }

    public function findById(int $id, array $columns = ['*'])
    {
        $entity = $this->model->select($columns)->find($id);

        if ($entity) {

            return $entity;
        }

        return false;
    }

    public function create(array $data = [])
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data = [])
    {
        return $this->model->find($id)->update($data);
    }

    public function delete(int $id)
    {
        return $this->model->delete($id);
    }

    public function forceDelete(int $id)
    {
        return $this->model->forceDelete($id);
    }

    public function filter(
        array $params = [],
        array $columns = ['*'],
        array $filters = [],
        int $perPage = 15,
        array $orderBy = ['id', 'DESC'],
        array $relations = [],
        array $sorts = []
    ) {
        $query = $this->model->select($columns)->with($relations);

        foreach ($filters as $field => $operator) {
            if (isset($params[$field]) && $params[$field] !== null) {
                $value = $params[$field];
                if ($operator === 'like') {
                    $query->where($field, 'like', "%$value%");
                } else {
                    $query->where($field, $operator, $value);
                }
            }
        }

        if (!empty($sorts)) {
            foreach ($sorts as $field => $direction) {
                $query->orderBy($field, $direction);
            }
        } else {
            $query->orderBy($orderBy[0], $orderBy[1]);
        }

        return $query->paginate($perPage)->withQueryString();
    }
}
