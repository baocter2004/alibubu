<?php

namespace App\Repositories;

use App\Models\AttributeValue;
use App\Repositories\BaseRepository;

class AttributeValueRepository extends BaseRepository
{
    public function getModel(): AttributeValue
    {
        if (empty($this->model)) {
            $this->model = app()->make(AttributeValue::class);
        }
        return $this->model;
    }
}
