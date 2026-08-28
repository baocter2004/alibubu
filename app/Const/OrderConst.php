<?php

namespace App\Const;

class OrderConst
{
    const STATUS_PENDING = 1;
    const STATUS_CONFIRMED = 2;
    const STATUS_SHIPPING = 3;
    const STATUS_COMPLETED = 4;
    const STATUS_CANCELLED = 5;

    public static function statuses(): array
    {
        return __('enum.order.status');
    }

    public static function statusLabel(?int $status): string
    {
        return self::statuses()[$status] ?? '-';
    }

    public static function statusBadgeClass(?int $status): string
    {
        return match ($status) {
            self::STATUS_PENDING => 'text-amber-600 bg-amber-100',
            self::STATUS_CONFIRMED => 'text-sky-600 bg-sky-100',
            self::STATUS_SHIPPING => 'text-indigo-600 bg-indigo-100',
            self::STATUS_COMPLETED => 'text-green-600 bg-green-100',
            self::STATUS_CANCELLED => 'text-red-600 bg-red-100',
            default => 'text-gray-600 bg-gray-100',
        };
    }

    public static function allowedTransitions(?int $status): array
    {
        return match ($status) {
            self::STATUS_PENDING => [self::STATUS_CONFIRMED, self::STATUS_CANCELLED],
            self::STATUS_CONFIRMED => [self::STATUS_SHIPPING, self::STATUS_CANCELLED],
            self::STATUS_SHIPPING => [self::STATUS_COMPLETED, self::STATUS_CANCELLED],
            default => [],
        };
    }

    public static function isFinal(?int $status): bool
    {
        return in_array($status, [self::STATUS_COMPLETED, self::STATUS_CANCELLED], true);
    }
}
