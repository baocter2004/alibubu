<?php

namespace App\Const;

class CouponConst
{
    const FIX_AMOUNT = 1;
    const PERCENT = 2;

    public static function types(): array
    {
        return __('enum.coupon.discount_type');
    }

    public static function typeLabel(?int $type): string
    {
        return self::types()[$type] ?? '-';
    }
}
