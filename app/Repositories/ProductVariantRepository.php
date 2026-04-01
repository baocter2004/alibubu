<?php

namespace App\Repositories;

use App\Models\ProductVariant;
use App\Repositories\BaseRepository;

class ProductVariantRepository extends BaseRepository
{
    public function getModel():ProductVariant
    {
        if (empty($this->model)) {
            $this->model = app()->make(ProductVariant::class);
        }
        return $this->model;
    }
}