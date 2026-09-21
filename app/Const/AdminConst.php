<?php

namespace App\Const;

class AdminConst
{
    const ROLE_SUPER_ADMIN = 1;
    const ROLE_MANAGER = 2;
    const ROLE_STAFF = 3;

    const ROLE_NAMES = [
        'SUPER_ADMIN' => self::ROLE_SUPER_ADMIN,
        'MANAGER' => self::ROLE_MANAGER,
        'STAFF' => self::ROLE_STAFF,
    ];

    public static function roles(): array
    {
        return __('enum.admin.role');
    }

    public static function roleLabel(?int $role): string
    {
        return self::roles()[$role] ?? '-';
    }

    public static function allRoleIds(): array
    {
        return [self::ROLE_SUPER_ADMIN, self::ROLE_MANAGER, self::ROLE_STAFF];
    }

    public static function managementRoleIds(): array
    {
        return [self::ROLE_SUPER_ADMIN, self::ROLE_MANAGER];
    }

    public static function editableRoleIds(): array
    {
        return [self::ROLE_MANAGER, self::ROLE_STAFF];
    }

    public static function isValidRole(mixed $role): bool
    {
        return is_numeric($role) && in_array((int) $role, self::allRoleIds(), true);
    }

    public static function resolveRole(string $role): ?int
    {
        if (ctype_digit($role)) {
            return self::isValidRole($role) ? (int) $role : null;
        }

        return self::ROLE_NAMES[strtoupper($role)] ?? null;
    }
}
