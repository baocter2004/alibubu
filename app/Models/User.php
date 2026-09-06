<?php

namespace App\Models;

use App\Const\UserConst;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasUuids;

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

    public function isLocked(): bool
    {
        return (int) $this->status === UserConst::STATUS_LOCKED;
    }

    protected ?array $wishlistedProductIds = null;

    // Relations
    public function userAddresses(): HasMany
    {
        return $this->hasMany(UserAddress::class);
    }

    public function defaultAddress(): HasMany
    {
        return $this->userAddresses()->where('is_default', true);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function hasWishlisted(string $productId): bool
    {
        return in_array($productId, $this->wishlistedProductIds(), true);
    }

    public function wishlistedProductIds(): array
    {
        if ($this->wishlistedProductIds === null) {
            $this->wishlistedProductIds = $this->wishlists()
                ->pluck('product_id')
                ->map(fn ($id) => (string) $id)
                ->all();
        }

        return $this->wishlistedProductIds;
    }

    public function forgetWishlistCache(): void
    {
        $this->wishlistedProductIds = null;
    }
}
