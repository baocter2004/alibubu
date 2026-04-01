<?php

namespace App\Repositories;

use App\Models\CouponRestriction;
use App\Repositories\BaseRepository;

class CounponRestrictionRepository extends BaseRepository
{
    public function getModel(): CouponRestriction
    {
        if (empty($this->model)) {
            $this->model = app()->make(CouponRestriction::class);
        }
        return $this->model;
    }
}