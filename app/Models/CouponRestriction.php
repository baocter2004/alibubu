<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CouponRestriction extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'coupon_id',
        'min_order_value',
        'max_discount_value',
        'valid_categories',
        'valid_products',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'min_order_value' => 'decimal:2',
            'max_discount_value' => 'decimal:2',
            'valid_categories' => 'array',
            'valid_products' => 'array',
        ];
    }

    // Relations
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }
}
