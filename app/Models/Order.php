<?php

namespace App\Models;

use App\Const\OrderConst;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'user_id',
        'phone_number',
        'email',
        'fullname',
        'address',
        'note',
        'total_amount',
        'status',
        'confirmed_at',
        'completed_at',
        'cancelled_at',
        'cancel_reason',
        'is_paid',
        'payment_method',
        'is_refund',
        'locked_status',
        'coupon_id',
        'coupon_code',
        'coupon_description',
        'coupon_discount_type',
        'coupon_discount_value',
        'max_discount_value',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'status' => 'integer',
            'confirmed_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'is_paid' => 'boolean',
            'payment_method' => 'integer',
            'is_refund' => 'boolean',
            'locked_status' => 'boolean',
            'coupon_discount_value' => 'decimal:2',
            'max_discount_value' => 'decimal:2',
        ];
    }

    // Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function canTransitionTo(int $status): bool
    {
        return in_array($status, OrderConst::allowedTransitions($this->status), true);
    }

    public function getTotalQuantityAttribute(): int
    {
        return (int) $this->items->sum('quantity');
    }
}
