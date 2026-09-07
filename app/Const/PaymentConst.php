<?php

namespace App\Const;

class PaymentConst
{
    const METHOD_COD = 1;
    const METHOD_BANK_TRANSFER = 2;

    const METHOD_VNPAY = 3;

    const METHOD_MOMO = 4;

    public static function methods(): array
    {
        return __('enum.payment.method');
    }

    public static function methodLabel(?int $method): string
    {
        return self::methods()[$method] ?? '-';
    }

    public static function isOnline(?int $method): bool
    {
        return in_array($method, [self::METHOD_VNPAY, self::METHOD_MOMO], true);
    }

    public static function methodIcon(?int $method): string
    {
        return match ($method) {
            self::METHOD_BANK_TRANSFER => 'fa-building-columns',
            self::METHOD_VNPAY => 'fa-credit-card',
            self::METHOD_MOMO => 'fa-wallet',
            default => 'fa-money-bill-wave',
        };
    }
}
