<?php

namespace App\Repositories;

use App\Models\Order;

class OrderRepository extends BaseRepository
{
    public function getModel(): Order
    {
        if (empty($this->model)) {
            $this->model = app()->make(Order::class);
        }

        return $this->model;
    }
}
