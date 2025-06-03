<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository extends BaseRepository
{
    public function getModel(): string
    {
        return User::class;
    }

    public function findBy(string $field, $value, array $columns = ['*'])
    {
        $entity = $this->model->select($columns)->where($field, $value)->first();

        if ($entity) {
            return $entity;
        }

        return false;
    }
}
