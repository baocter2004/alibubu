<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, HasApiTokens;

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
        'google_id',
        'remember_token'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birthday' => 'datetime',
            'loyalty_points'    => 'integer',
            'status'            => 'integer',
            'role'              => 'integer',
            'gender'            => 'integer',
        ];
    }

    public function getEmailForVerification()
    {
        return $this->email;
    }

    // Relations
    public function userAddresses()
    {
        return $this->hasMany(UserAddress::class);
    }
}
