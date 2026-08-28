<?php

return [
    'province' => [
        'title' => 'Tỉnh/Thành phố',
        'subtitle' => 'Đơn vị hành chính dùng cho địa chỉ giao hàng.',
        'show' => 'Chi tiết tỉnh/thành phố',
        'fields' => [
            'name' => 'Tên',
            'code' => 'Mã',
            'codename' => 'Tên mã',
            'division_type' => 'Loại đơn vị',
            'phone_code' => 'Mã vùng',
            'wards_count' => 'Phường/Xã',
            'addresses_count' => 'Địa chỉ',
        ],
    ],

    'ward' => [
        'title' => 'Phường/Xã',
        'subtitle' => 'Đơn vị hành chính cấp phường/xã.',
        'show' => 'Chi tiết phường/xã',
        'fields' => [
            'name' => 'Tên',
            'code' => 'Mã',
            'codename' => 'Tên mã',
            'division_type' => 'Loại đơn vị',
            'province' => 'Tỉnh/Thành phố',
            'addresses_count' => 'Địa chỉ',
        ],
    ],
];
