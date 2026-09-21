<?php

namespace App\Models;

use App\Const\OrderConst;
use App\Const\PaymentConst;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasUuids;

    protected $fillable = [
        'code',
        'user_id',
        'phone_number',
        'email',
        'fullname',
        'address',
        'note',
        'locale',
        'total_amount',
        'status',
        'confirmed_at',
        'shipped_at',
        'completed_at',
        'cancelled_at',
        'returned_at',
        'cancel_reason',
        'is_paid',
        'payment_status',
        'payment_method',
        'payment_reference',
        'paid_at',
        'payment_expires_at',
        'refunded_at',
        'refund_note',
        'is_refund',
        'locked_status',
        'coupon_id',
        'coupon_code',
        'coupon_description',
        'coupon_discount_type',
        'coupon_discount_value',
        'membership_tier',
        'membership_discount',
        'max_discount_value',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'membership_discount' => 'decimal:2',
            'status' => 'integer',
            'confirmed_at' => 'datetime',
            'shipped_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'returned_at' => 'datetime',
            'is_paid' => 'boolean',
            'payment_status' => 'integer',
            'payment_method' => 'integer',
            'paid_at' => 'datetime',
            'payment_expires_at' => 'datetime',
            'refunded_at' => 'datetime',
            'is_refund' => 'boolean',
            'locked_status' => 'boolean',
            'coupon_discount_value' => 'decimal:2',
            'max_discount_value' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Order $order) {
            if ($order->isDirty('payment_status') || ! $order->exists) {
                $order->payment_status = $order->payment_status
                    ?: ($order->is_paid ? PaymentConst::STATUS_PAID : PaymentConst::STATUS_UNPAID);
                $order->is_paid = (int) $order->payment_status === PaymentConst::STATUS_PAID;
            } elseif ($order->isDirty('is_paid')) {
                $order->payment_status = $order->is_paid ? PaymentConst::STATUS_PAID : PaymentConst::STATUS_UNPAID;
            }
        });
    }

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

    public function histories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->orderBy('created_at')->orderBy('id');
    }

    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class)->latest();
    }

    public function canTransitionTo(int $status): bool
    {
        return in_array($status, OrderConst::allowedTransitions($this->status), true);
    }

    public function isPaid(): bool
    {
        return (int) $this->payment_status === PaymentConst::STATUS_PAID;
    }

    public function isAwaitingPayment(): bool
    {
        return $this->status === OrderConst::STATUS_PENDING
            && PaymentConst::requiresPrepayment($this->payment_method)
            && PaymentConst::isPayable($this->payment_status)
            && (float) $this->total_amount > 0;
    }

    public function canPayOnline(): bool
    {
        return $this->isAwaitingPayment() && PaymentConst::isOnline($this->payment_method);
    }

    public function isCancellableByCustomer(): bool
    {
        return OrderConst::isCancellableByCustomer($this->status);
    }

    public function customerEmail(): ?string
    {
        return $this->email ?: $this->user?->email;
    }

    public function customerUrl(): string
    {
        return $this->user_id
            ? route('account.orders.show', $this->id)
            : route('order.track', ['code' => $this->code]);
    }

    public function getSubtotalAmountAttribute(): float
    {
        if ($this->relationLoaded('items')) {
            return (float) $this->items->sum(fn (OrderItem $item) => (float) $item->price * (int) $item->quantity);
        }

        return (float) $this->total_amount
            + (float) $this->coupon_discount_value
            + (float) $this->membership_discount;
    }

    public function getTotalQuantityAttribute(): int
    {
        return (int) $this->items->sum('quantity');
    }
}
