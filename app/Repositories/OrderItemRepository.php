<?php

namespace App\Repositories;

use App\Models\OrderItem;
use App\Repositories\BaseRepository;

class OrderItemRepository extends BaseRepository
{
    public function getModel(): OrderItem
    {
        if (empty($this->model)) {
            $this->model = app()->make(OrderItem::class);
        }
        return $this->model;
    }
}
