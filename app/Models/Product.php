<?php

namespace App\Models;

use App\Const\ProductConst;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'branch_id',
        'name',
        'slug',
        'views',
        'rating',
        'stock',
        'sold',
        'short_descriptions',
        'descriptions',
        'thumbnail',
        'type',
        'sku',
        'price',
        'sale_price',
        'sale_price_start_at',
        'sale_price_end_at',
        'is_sale',
        'is_featured',
        'is_trending',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'views' => 'integer',
            'rating' => 'decimal:2',
            'stock' => 'integer',
            'sold' => 'integer',
            'type' => 'integer',
            'price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'sale_price_start_at' => 'datetime',
            'sale_price_end_at' => 'datetime',
            'is_sale' => 'boolean',
            'is_featured' => 'boolean',
            'is_trending' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    // Relations
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_product');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'product_tag');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function galleries(): HasMany
    {
        return $this->hasMany(ProductGallery::class);
    }

    public function accessories(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'product_accessory', 'product_id', 'product_accessory_id');
    }

    public function hasVariants(): bool
    {
        return $this->type === ProductConst::VARIANT;
    }

    public function getEffectivePriceAttribute(): ?float
    {
        if ($this->hasVariants()) {
            $variant = $this->variants->sortBy(fn ($item) => $item->effective_price)->first();

            return $variant?->effective_price;
        }

        return $this->onSale() ? (float) $this->sale_price : (float) $this->price;
    }

    public function getBasePriceAttribute(): ?float
    {
        if ($this->hasVariants()) {
            return $this->variants->sortBy(fn ($item) => $item->effective_price)->first()?->price;
        }

        return $this->price === null ? null : (float) $this->price;
    }

    public function getDiscountPercentAttribute(): int
    {
        $base = $this->base_price;
        $effective = $this->effective_price;

        if (! $base || ! $effective || $effective >= $base) {
            return 0;
        }

        return (int) round((($base - $effective) / $base) * 100);
    }

    public function inStock(): bool
    {
        return $this->stock > 0;
    }

    public function onSale(): bool
    {
        if (! $this->is_sale || $this->sale_price === null) {
            return false;
        }

        $now = now();

        if ($this->sale_price_start_at && $now->lt($this->sale_price_start_at)) {
            return false;
        }

        if ($this->sale_price_end_at && $now->gt($this->sale_price_end_at)) {
            return false;
        }

        return true;
    }
}
