<?php

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
    ],
];
