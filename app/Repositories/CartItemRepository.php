<?php

namespace App\Repositories;

use App\Models\CartItem;
use App\Repositories\BaseRepository;

class CartItemRepository extends BaseRepository
{
    public function getModel(): CartItem
    {
        if (empty($this->model)) {
            $this->model = app()->make(CartItem::class);
        }
        return $this->model;
    }
}