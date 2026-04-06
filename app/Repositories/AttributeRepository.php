<?php

namespace App\Repositories;

use App\Models\Attribute;
use App\Repositories\BaseRepository;

class AttributeRepository extends BaseRepository
{
    public function getModel(): Attribute
    {
        if (empty($this->model)) {
            $this->model = app()->make(Attribute::class);
        }
        return $this->model;
    }
}
