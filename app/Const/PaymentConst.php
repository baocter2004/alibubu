<?php

namespace App\Const;

class PaymentConst
{
    const METHOD_COD = 1;
    const METHOD_BANK_TRANSFER = 2;
    const METHOD_VNPAY = 3;
    const METHOD_MOMO = 4;

    const STATUS_UNPAID = 1;
    const STATUS_PAID = 2;
    const STATUS_REFUND_PENDING = 3;
    const STATUS_REFUNDED = 4;
    const STATUS_FAILED = 5;

    const GATEWAY_VNPAY = 'vnpay';
    const GATEWAY_MOMO = 'momo';
    const GATEWAY_MANUAL = 'manual';
    const GATEWAY_COD = 'cod';

    const TYPE_PAYMENT = 'payment';
    const TYPE_REFUND = 'refund';

    const VNPAY_SUCCESS_CODE = '00';
    const VNPAY_VERSION = '2.1.0';
    const VNPAY_COMMAND_PAY = 'pay';
    const VNPAY_ORDER_TYPE = 'other';
    const VNPAY_AMOUNT_MULTIPLIER = 100;
    const VNPAY_MIN_AMOUNT = 5000;
    const VNPAY_MAX_AMOUNT = 999999999;
    const VNPAY_TIMEZONE = 'Asia/Ho_Chi_Minh';
    const VNPAY_DATE_FORMAT = 'YmdHis';

    const VNPAY_RSP_SUCCESS = '00';
    const VNPAY_RSP_ORDER_NOT_FOUND = '01';
    const VNPAY_RSP_ALREADY_CONFIRMED = '02';
    const VNPAY_RSP_INVALID_AMOUNT = '04';
    const VNPAY_RSP_INVALID_SIGNATURE = '97';
    const VNPAY_RSP_UNKNOWN_ERROR = '99';

    const MOMO_SUCCESS_CODE = 0;
    const MOMO_MIN_AMOUNT = 1000;
    const MOMO_MAX_AMOUNT = 50000000;
    const MOMO_CONNECT_TIMEOUT = 5;
    const MOMO_TIMEOUT = 15;

    const RESULT_PAID = 'paid';
    const RESULT_FAILED = 'failed';
    const RESULT_ALREADY_CONFIRMED = 'already_confirmed';
    const RESULT_AMOUNT_MISMATCH = 'amount_mismatch';
    const RESULT_ORDER_NOT_FOUND = 'order_not_found';
    const RESULT_INVALID_SIGNATURE = 'invalid_signature';
    const RESULT_GATEWAY_DISABLED = 'gateway_disabled';
    const RESULT_REFUND_PENDING = 'refund_pending';
    const RESULT_PENDING = 'pending';
    const RESULT_ERROR = 'error';

    const PROBLEM_AMOUNT_MISMATCH = 'amount_mismatch';
    const PROBLEM_PAID_AFTER_CANCEL = 'paid_after_cancel';
    const PROBLEM_DUPLICATE_PAYMENT = 'duplicate_payment';

    const REFERENCE_SUFFIX_LENGTH = 6;

    const VIETQR_IMAGE_URL = 'https://img.vietqr.io/image/%s-%s-%s.png';

    const SOURCE_REQUEST = 'request';
    const SOURCE_RETURN = 'return';
    const SOURCE_IPN = 'ipn';
    const SOURCE_ADMIN = 'admin';
    const SOURCE_SYSTEM = 'system';

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

    public static function requiresPrepayment(?int $method): bool
    {
        return in_array($method, [self::METHOD_BANK_TRANSFER, self::METHOD_VNPAY, self::METHOD_MOMO], true);
    }

    public static function gatewayFor(?int $method): ?string
    {
        return match ($method) {
            self::METHOD_VNPAY => self::GATEWAY_VNPAY,
            self::METHOD_MOMO => self::GATEWAY_MOMO,
            default => null,
        };
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

    public static function statuses(): array
    {
        return __('enum.payment.status');
    }

    public static function statusLabel(?int $status): string
    {
        return self::statuses()[$status] ?? '-';
    }

    public static function statusBadgeClass(?int $status): string
    {
        return match ($status) {
            self::STATUS_PAID => 'text-green-700 bg-green-100',
            self::STATUS_REFUND_PENDING => 'text-orange-800 bg-orange-100',
            self::STATUS_REFUNDED => 'text-slate-700 bg-slate-200',
            self::STATUS_FAILED => 'text-red-700 bg-red-100',
            default => 'text-amber-800 bg-amber-100',
        };
    }

    public static function payableStatuses(): array
    {
        return [self::STATUS_UNPAID, self::STATUS_FAILED];
    }

    public static function isPayable(?int $status): bool
    {
        return in_array($status, self::payableStatuses(), true);
    }

    public static function gatewayLabel(?string $gateway): string
    {
        return __('enum.payment.gateway.' . ($gateway ?: self::GATEWAY_MANUAL));
    }
}
