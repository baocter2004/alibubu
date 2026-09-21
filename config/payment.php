<?php

use App\Const\PaymentConst;

return [
    'vnpay' => [
        'enabled' => env('VNPAY_ENABLED', false),
        'tmn_code' => env('VNPAY_TMN_CODE'),
        'hash_secret' => env('VNPAY_HASH_SECRET'),
        'endpoint' => env('VNPAY_ENDPOINT', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'),
        'return_url' => env('VNPAY_RETURN_URL'),
        'locale' => env('VNPAY_LOCALE', 'vn'),
        'currency' => env('VNPAY_CURRENCY', 'VND'),
        'expire_minutes' => (int) env('VNPAY_EXPIRE_MINUTES', 15),
        'timezone' => PaymentConst::VNPAY_TIMEZONE,
        'min_amount' => (int) env('VNPAY_MIN_AMOUNT', PaymentConst::VNPAY_MIN_AMOUNT),
        'max_amount' => (int) env('VNPAY_MAX_AMOUNT', PaymentConst::VNPAY_MAX_AMOUNT),
    ],
    'momo' => [
        'enabled' => env('MOMO_ENABLED', false),
        'partner_code' => env('MOMO_PARTNER_CODE'),
        'access_key' => env('MOMO_ACCESS_KEY'),
        'secret_key' => env('MOMO_SECRET_KEY'),
        'endpoint' => env('MOMO_ENDPOINT', 'https://test-payment.momo.vn/v2/gateway/api/create'),
        'return_url' => env('MOMO_RETURN_URL'),
        'ipn_url' => env('MOMO_IPN_URL'),
        'request_type' => env('MOMO_REQUEST_TYPE', 'captureWallet'),
        'lang' => env('MOMO_LANG', 'vi'),
        'min_amount' => (int) env('MOMO_MIN_AMOUNT', PaymentConst::MOMO_MIN_AMOUNT),
        'max_amount' => (int) env('MOMO_MAX_AMOUNT', PaymentConst::MOMO_MAX_AMOUNT),
        'connect_timeout' => (int) env('MOMO_CONNECT_TIMEOUT', PaymentConst::MOMO_CONNECT_TIMEOUT),
        'timeout' => (int) env('MOMO_TIMEOUT', PaymentConst::MOMO_TIMEOUT),
    ],
    'bank_transfer' => [
        'enabled' => env('BANK_TRANSFER_ENABLED', true),
        'bank_code' => env('BANK_TRANSFER_BANK_CODE'),
        'account_number' => env('BANK_TRANSFER_ACCOUNT_NUMBER'),
        'account_name' => env('BANK_TRANSFER_ACCOUNT_NAME'),
        'branch' => env('BANK_TRANSFER_BRANCH'),
        'qr_enabled' => env('BANK_TRANSFER_QR_ENABLED', true),
        'qr_template' => env('BANK_TRANSFER_QR_TEMPLATE', 'compact2'),
    ],
    'settle_on_return' => env('PAYMENT_SETTLE_ON_RETURN', false),
];
