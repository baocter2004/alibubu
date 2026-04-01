<?php

namespace App\Repositories;

use App\Models\Coupon;
use App\Repositories\BaseRepository;

class CouponRepository extends BaseRepository
{
    public function getModel():Coupon
    {
        if (empty($this->model)) {
            $this->model = app()->make(Coupon::class);
        }
        return $this->model;
    }
}