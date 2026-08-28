<?php

return [
    'user' => [
        'role' => [
            1 => 'Customer',
            2 => 'Staff',
            3 => 'Administrator',
        ],
        'status' => [
            1 => 'Active',
            2 => 'Inactive',
            3 => 'Locked',
        ],
        'gender' => [
            1 => 'Male',
            2 => 'Female',
            3 => 'Other',
        ],
    ],

    'global' => [
        'status' => [
            0 => 'Inactive',
            1 => 'Active',
        ],
    ],

    'coupon' => [
        'discount_type' => [
            1 => 'Fixed amount',
            2 => 'Percentage',
        ],
    ],

    'product' => [
        'type' => [
            0 => 'Simple',
            1 => 'Variable',
        ],
    ],
];
