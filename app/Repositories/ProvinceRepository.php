<?php

namespace App\Repositories;

use App\Models\Province;

class ProvinceRepository extends BaseRepository
{
    public function getModel(): Province
    {
        if(empty($this->model)) {
            $this->model = app()->make(Province::class);
        }
        return $this->model;
    }
}