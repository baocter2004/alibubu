<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminRolePermission extends Model
{
    protected $fillable = [
        'role',
        'permission',
    ];

    protected function casts(): array
    {
        return [
            'role' => 'integer',
        ];
    }
}
