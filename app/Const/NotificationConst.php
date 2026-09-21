<?php

namespace App\Const;

class NotificationConst
{
    const ADMIN_PER_PAGE = 20;
    const CLIENT_PER_PAGE = 15;
    const LATEST_LIMIT = 6;

    const FILTER_ALL = 'all';
    const FILTER_UNREAD = 'unread';

    const LEVEL_INFO = 'info';
    const LEVEL_SUCCESS = 'success';
    const LEVEL_WARNING = 'warning';
    const LEVEL_DANGER = 'danger';

    const DEFAULT_ICON = 'fa-bell';

    public static function filters(): array
    {
        return [self::FILTER_ALL, self::FILTER_UNREAD];
    }

    public static function levels(): array
    {
        return [self::LEVEL_INFO, self::LEVEL_SUCCESS, self::LEVEL_WARNING, self::LEVEL_DANGER];
    }

    public static function levelIconClass(?string $level, bool $isRead = false): string
    {
        if ($isRead) {
            return 'bg-gray-100 text-gray-500';
        }

        return match ($level) {
            self::LEVEL_SUCCESS => 'bg-green-100 text-green-700',
            self::LEVEL_WARNING => 'bg-amber-100 text-amber-700',
            self::LEVEL_DANGER => 'bg-red-100 text-red-700',
            default => 'bg-sky-100 text-sky-700',
        };
    }
}
