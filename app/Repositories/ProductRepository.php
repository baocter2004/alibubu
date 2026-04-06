<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\BaseRepository;

class ProductRepository extends BaseRepository
{
    public function getModel():Product 
    {
        if (empty($this->model)) {
            $this->model = app()->make(Product::class);
        }
        return $this->model;
    }
}