<?php

use App\Models\Coupon;
use App\Repositories\BaseRepository;

class CouponRepository extends BaseRepository
{
    public function getModel():string
    {
        return Coupon::class;
    }
}