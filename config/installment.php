<?php

return [
    'enabled' => env('INSTALLMENT_ENABLED', true),
    'min_amount' => (int) env('INSTALLMENT_MIN_AMOUNT', 3000000),
    'terms' => [6, 12],
    'default_term' => (int) env('INSTALLMENT_DEFAULT_TERM', 12),
];
