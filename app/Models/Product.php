<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [];

    // Relations
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
