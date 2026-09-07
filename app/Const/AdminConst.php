<?php

namespace App\Const;

class AdminConst
{
    const ROLE_SUPER_ADMIN = 1;
    const ROLE_MANAGER = 2;
    const ROLE_STAFF = 3;

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
}
