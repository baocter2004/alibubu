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
            self::STATUS_PENDING => 'text-amber-800 bg-amber-100',
            self::STATUS_CONFIRMED => 'text-sky-700 bg-sky-100',
            self::STATUS_SHIPPING => 'text-indigo-700 bg-indigo-100',
            self::STATUS_COMPLETED => 'text-green-800 bg-green-100',
            self::STATUS_CANCELLED => 'text-red-700 bg-red-100',
            default => 'text-gray-700 bg-gray-100',
        };
    }

    public static function statusActionClass(?int $status): string
    {
        return match ($status) {
            self::STATUS_CONFIRMED => 'text-white bg-primary hover:bg-primary-hover',
            self::STATUS_SHIPPING => 'text-white bg-sky-600 hover:bg-sky-700',
            self::STATUS_COMPLETED => 'text-white bg-emerald-600 hover:bg-emerald-700',
            self::STATUS_CANCELLED => 'text-red-600 border border-red-200 bg-white hover:bg-red-50',
            default => 'text-gray-700 border border-gray-200 bg-white hover:bg-gray-50',
        };
    }

    public static function statusActionIcon(?int $status): string
    {
        return match ($status) {
            self::STATUS_CONFIRMED => 'fa-circle-check',
            self::STATUS_SHIPPING => 'fa-truck-fast',
            self::STATUS_COMPLETED => 'fa-flag-checkered',
            self::STATUS_CANCELLED => 'fa-ban',
            default => 'fa-arrows-rotate',
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

    public static function customerCancellableStatuses(): array
    {
        return [self::STATUS_PENDING, self::STATUS_CONFIRMED];
    }

    public static function isCancellableByCustomer(?int $status): bool
    {
        return in_array($status, self::customerCancellableStatuses(), true);
    }
}
