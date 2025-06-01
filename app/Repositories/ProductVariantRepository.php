<?php

use App\Models\ProductVariant;
use App\Repositories\BaseRepository;

class ProductVariantRepository extends BaseRepository
{
    public function getModel():string
    {
        return ProductVariant::class;
    }
}