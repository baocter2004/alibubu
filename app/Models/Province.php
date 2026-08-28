<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    use HasUuids;

    protected $fillable = ['name', 'code', 'division_type', 'codename', 'phone_code'];

    // Relations
    public function userAddresses()
    {
        return $this->hasMany(UserAddress::class);
    }

    public function wards ()
    {
        return $this->hasMany(Ward::class, 'province_id', 'id');
    }
}
