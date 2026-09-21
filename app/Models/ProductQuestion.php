<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductQuestion extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'product_id',
        'user_id',
        'fullname',
        'email',
        'ip_address',
        'question',
        'answer',
        'answered_by',
        'answered_at',
        'is_published',
    ];

    protected $hidden = [
        'email',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'answered_at' => 'datetime',
            'is_published' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'answered_by');
    }

    public function isAnswered(): bool
    {
        return filled($this->answer);
    }

    public function askerName(): ?string
    {
        return $this->user?->fullname ?: $this->fullname;
    }

    public function contactEmail(): ?string
    {
        return $this->user?->email ?: $this->email;
    }
}
