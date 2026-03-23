<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\BaseRepository;

class ProductRepository extends BaseRepository
{
    public function getModel():string 
    {
        return Product::class;
    }
}