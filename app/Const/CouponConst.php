<?php 

namespace App\Const;

class CouponConst 
{
    const FIX_AMOUNT = 1;
    const PERCENT = 2;

    public static function getTypeList()
    {
        return [
            self::FIX_AMOUNT => 'FIX AMOUNT',
            self::PERCENT => 'PERCENT',
        ];
    }
}