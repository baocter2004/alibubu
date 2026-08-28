<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAddress extends Model
{
    use HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'province_id',
        'ward_id',
        'province',
        'ward',
        'address',
        'phone_number',
        'fullname',
        'is_default',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    // Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function provinceRel(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    public function wardRel(): BelongsTo
    {
        return $this->belongsTo(Ward::class, 'ward_id');
    }

    public function getFullAddressAttribute(): string
    {
        return collect([$this->address, $this->ward, $this->province])
            ->filter()
            ->implode(', ');
    }
}
