<?php

return [
    'province' => [
        'title' => 'Provinces & cities',
        'subtitle' => 'Administrative units used for delivery addresses.',
        'show' => 'Province detail',
        'fields' => [
            'name' => 'Name',
            'code' => 'Code',
            'codename' => 'Code name',
            'division_type' => 'Division type',
            'phone_code' => 'Phone code',
            'wards_count' => 'Wards',
            'addresses_count' => 'Addresses',
        ],
    ],

    'ward' => [
        'title' => 'Wards & communes',
        'subtitle' => 'Ward-level administrative units.',
        'show' => 'Ward detail',
        'fields' => [
            'name' => 'Name',
            'code' => 'Code',
            'codename' => 'Code name',
            'division_type' => 'Division type',
            'province' => 'Province / City',
            'addresses_count' => 'Addresses',
        ],
    ],
];
