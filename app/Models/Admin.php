<?php

namespace App\Models;

use App\Const\AdminConst;
use App\Notifications\AdminResetPassword;
use App\Services\Admin\RoleService;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasUuids;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'role' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new AdminResetPassword($token));
    }

    public function isSuperAdmin(): bool
    {
        return (int) $this->role === AdminConst::ROLE_SUPER_ADMIN;
    }

    public function isActive(): bool
    {
        return (bool) ($this->is_active ?? true);
    }

    public function roleLabel(): string
    {
        return AdminConst::roleLabel($this->role);
    }

    public function permissions(): array
    {
        if (! $this->isActive() || ! AdminConst::isValidRole($this->role)) {
            return [];
        }

        return app(RoleService::class)->permissionsFor((int) $this->role);
    }

    public function hasPermission(string $permission): bool
    {
        if (! $this->isActive()) {
            return false;
        }

        if ($this->isSuperAdmin()) {
            return true;
        }

        return in_array($permission, $this->permissions(), true);
    }
}
