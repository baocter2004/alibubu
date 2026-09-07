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
];
