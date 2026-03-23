<?php

namespace App\Repositories;

use App\Models\CouponRestriction;
use App\Repositories\BaseRepository;

class CounponRestrictionRepository extends BaseRepository
{
    public function getModel(): string
    {
        return CouponRestriction::class;
    }
}