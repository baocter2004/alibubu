<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CouponUser extends Pivot
{
    protected $table = 'coupon_user';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'coupon_id',
        'times_used',
        'used_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'times_used' => 'integer',
            'used_at' => 'datetime',
        ];
    }
}
