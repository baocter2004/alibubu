<?php

namespace App\Const;

class PaymentConst
{
    const METHOD_COD = 1;
    const METHOD_BANK_TRANSFER = 2;

    public static function methods(): array
    {
        return __('enum.payment.method');
    }

    public static function methodLabel(?int $method): string
    {
        return self::methods()[$method] ?? '-';
    }

    public static function methodIcon(?int $method): string
    {
        return match ($method) {
            self::METHOD_BANK_TRANSFER => 'fa-building-columns',
            default => 'fa-money-bill-wave',
        };
    }
}
