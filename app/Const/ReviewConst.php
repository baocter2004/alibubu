<?php

namespace App\Const;

class ReviewConst
{
    const ADMIN_PER_PAGE = 15;
    const CLIENT_PER_PAGE = 5;
    const MAX_IMAGES = 4;
    const REASON_MAX_LENGTH = 500;

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING => __('admin/review.status.pending'),
            self::STATUS_APPROVED => __('admin/review.status.approved'),
            self::STATUS_REJECTED => __('admin/review.status.rejected'),
        ];
    }

    public static function statusLabel(?string $status): string
    {
        return self::statuses()[$status] ?? '-';
    }

    public static function statusBadgeClass(?string $status): string
    {
        return match ($status) {
            self::STATUS_APPROVED => 'text-green-700 bg-green-100',
            self::STATUS_REJECTED => 'text-red-700 bg-red-100',
            default => 'text-amber-800 bg-amber-100',
        };
    }
}
