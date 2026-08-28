<?php

namespace App\Const;

class GlobalConst
{
    const IS_NOT_ACTIVE = 0;
    const IS_ACTIVE = 1;

    const LIMIT = 10;

    public static function statuses(): array
    {
        return __('enum.global.status');
    }

    public static function statusLabel(int|bool|null $status): string
    {
        return self::statuses()[(int) $status] ?? '-';
    }

    public static function statusBadgeClass(int|bool|null $status): string
    {
        return (int) $status === self::IS_ACTIVE
            ? 'text-green-600 bg-green-100'
            : 'text-red-600 bg-red-100';
    }
}
