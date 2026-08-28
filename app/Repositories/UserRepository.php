<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository extends BaseRepository
{
    public function getModel(): User
    {
        if (empty($this->model)) {
            $this->model = app()->make(User::class);
        }

        return $this->model;
    }
}
