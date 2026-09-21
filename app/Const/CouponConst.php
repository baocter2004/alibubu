<?php

namespace App\Const;

class CouponConst
{
    const FIX_AMOUNT = 1;
    const PERCENT = 2;

    const SESSION_KEY = 'coupon_code';

    const AVAILABLE_SCAN_LIMIT = 30;

    const AVAILABLE_LIMIT = 6;

    public static function types(): array
    {
        return __('enum.coupon.discount_type');
    }

    public static function typeLabel(?int $type): string
    {
        return self::types()[$type] ?? '-';
    }
}
