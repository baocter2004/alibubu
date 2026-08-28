<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'product_variant_id',
        'name',
        'price',
        'old_price',
        'old_price_variant',
        'quantity',
        'name_variant',
        'attributes_variant',
        'price_variant',
        'quantity_variant',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'old_price' => 'decimal:2',
            'old_price_variant' => 'decimal:2',
            'price_variant' => 'decimal:2',
            'quantity' => 'integer',
            'quantity_variant' => 'integer',
            'attributes_variant' => 'array',
        ];
    }

    // Relations
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
