<?php

namespace App\Const;

class UserConst
{
    const ROLE_USER = 1;
    const ROLE_EMPLOYEE = 2;
    const ROLE_ADMIN = 3;

    const MALE = 1;
    const FEMALE = 2;
    const OTHER = 3;

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 2;
    const STATUS_LOCKED = 3;

    public static function roles(): array
    {
        return __('enum.user.role');
    }

    public static function statuses(): array
    {
        return __('enum.user.status');
    }

    public static function genders(): array
    {
        return __('enum.user.gender');
    }

    public static function roleLabel(?int $role): string
    {
        return self::roles()[$role] ?? '-';
    }

    public static function statusLabel(?int $status): string
    {
        return self::statuses()[$status] ?? '-';
    }

    public static function genderLabel(?int $gender): string
    {
        return self::genders()[$gender] ?? '-';
    }

    public static function statusBadgeClass(?int $status): string
    {
        return match ($status) {
            self::STATUS_ACTIVE => 'text-green-600 bg-green-100',
            self::STATUS_INACTIVE => 'text-amber-600 bg-amber-100',
            self::STATUS_LOCKED => 'text-red-600 bg-red-100',
            default => 'text-gray-600 bg-gray-100',
        };
    }
}
