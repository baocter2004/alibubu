<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'title',
        'description',
        'discount_type',
        'discount_value',
        'usage_limit',
        'usage_count',
        'is_expired',
        'is_active',
        'start_date',
        'end_date',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'discount_type' => 'integer',
            'discount_value' => 'decimal:2',
            'usage_limit' => 'integer',
            'usage_count' => 'integer',
            'is_expired' => 'boolean',
            'is_active' => 'boolean',
            'start_date' => 'datetime',
            'end_date' => 'datetime',
        ];
    }

    // Relations
    public function restriction(): HasOne
    {
        return $this->hasOne(CouponRestriction::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'coupon_user')
            ->withPivot(['times_used', 'used_at'])
            ->withTimestamps();
    }
}
