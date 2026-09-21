<?php

use App\Const\OrderConst;

return [
    'low_stock_threshold' => (int) env('ORDER_LOW_STOCK_THRESHOLD', OrderConst::LOW_STOCK_THRESHOLD),
    'expire_unpaid' => [
        'online_minutes' => (int) env(
            'ORDER_ONLINE_PAYMENT_MINUTES',
            ((int) env('VNPAY_EXPIRE_MINUTES', 15)) + OrderConst::ONLINE_PAYMENT_GRACE_MINUTES
        ),
        'bank_transfer_hours' => (int) env('ORDER_BANK_TRANSFER_HOURS', OrderConst::BANK_TRANSFER_HOLD_HOURS),
    ],
    'rate_limits' => [
        'checkout_per_minute' => (int) env('ORDER_CHECKOUT_PER_MINUTE', OrderConst::RATE_CHECKOUT_PER_MINUTE),
        'checkout_per_hour' => (int) env('ORDER_CHECKOUT_PER_HOUR', OrderConst::RATE_CHECKOUT_PER_HOUR),
        'coupon_per_minute' => (int) env('ORDER_COUPON_PER_MINUTE', OrderConst::RATE_COUPON_PER_MINUTE),
        'cart_per_minute' => (int) env('ORDER_CART_PER_MINUTE', OrderConst::RATE_CART_PER_MINUTE),
        'payment_retry_per_minute' => (int) env('ORDER_PAYMENT_RETRY_PER_MINUTE', OrderConst::RATE_PAYMENT_RETRY_PER_MINUTE),
    ],
];
