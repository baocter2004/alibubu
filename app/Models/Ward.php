<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Ward extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'code',
        'division_type',
        'codename',
        'province_id'
    ];

    // Relations
    public function userAddresses()
    {
        return $this->hasMany(UserAddress::class);
    }

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id', 'id');
    }
}
