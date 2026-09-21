<?php

namespace App\Models;

use App\Const\OrderConst;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderStatusHistory extends Model
{
    use HasUuids;

    const UPDATED_AT = null;

    protected $fillable = [
        'order_id',
        'from_status',
        'to_status',
        'event',
        'actor_type',
        'actor_id',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'from_status' => 'integer',
            'to_status' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'actor_id')->withTrashed();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id')->withTrashed();
    }

    public function actorName(): ?string
    {
        return match ($this->actor_type) {
            OrderConst::ACTOR_ADMIN => $this->admin?->name,
            OrderConst::ACTOR_CUSTOMER => $this->user?->fullname,
            default => null,
        };
    }

    public function isStatusChange(): bool
    {
        return $this->event === OrderConst::EVENT_STATUS_CHANGED || $this->event === OrderConst::EVENT_PLACED;
    }
}
