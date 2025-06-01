<?php

use App\Models\CartItem;
use App\Repositories\BaseRepository;

class CartItemRepository extends BaseRepository
{
    public function getModel(): string
    {
        return CartItem::class;
    }
}