<?php

namespace App\Repositories;

use App\Models\OrderItem;
use App\Repositories\BaseRepository;

class OrderItemRepository extends BaseRepository
{
    public function getModel():string 
    {
        return OrderItem::class;
    }
}