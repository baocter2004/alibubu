<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class UserRepository extends BaseRepository
{
    protected const KEYWORD_COLUMNS = ['users.fullname', 'users.email', 'users.phone_number'];

    public function getModel(): User
    {
        if (empty($this->model)) {
            $this->model = app()->make(User::class);
        }

        return $this->model;
    }

    public function filter(array $params): Builder
    {
        $query = parent::filter($params);

        $this->applyKeyword($query, $params['keyword'] ?? null, self::KEYWORD_COLUMNS);

        return $query;
    }
}
