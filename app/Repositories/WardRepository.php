<?php

namespace App\Repositories;

use App\Models\Ward;

class WardRepository extends BaseRepository
{
    public function getModel(): Ward
    {
        if(empty($this->model)) {
            $this->model = app()->make(Ward::class);
        }
        return $this->model;
    }
}