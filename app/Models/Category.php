<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'fullname',
        'email',
        'password',
        'email_verified_at',
        'avatar',
        'role',
        'phone_number',
        'gender',
        'birthday',
        'status',
        'reason_lock',
        'bank_name',
        'user_bank_name',
        'bank_account',
        'loyalty_points',
    ];
}
