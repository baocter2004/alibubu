<?php

use App\Models\Order;
use App\Repositories\BaseRepository;

class OrderRepository extends BaseRepository
{
    public function getModel():string
    {
        return Order::class;
    }
}