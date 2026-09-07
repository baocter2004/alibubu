<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPromotion extends Model
{
    use HasUuids;

    protected $fillable = [
        'product_id',
        'content',
        'icon',
        'ordinal',
    ];

    protected function casts(): array
    {
        return [
            'ordinal' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
