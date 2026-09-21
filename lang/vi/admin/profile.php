<?php

return [
    'title' => 'Tài khoản của tôi',
    'subtitle' => 'Cập nhật thông tin và mật khẩu tài khoản quản trị của bạn.',

    'sections' => [
        'information' => 'Thông tin tài khoản',
        'information_hint' => 'Tên và email dùng để đăng nhập trang quản trị.',
        'password' => 'Đổi mật khẩu',
        'password_hint' => 'Nên dùng mật khẩu tối thiểu 8 ký tự và không trùng mật khẩu cũ.',
    ],

    'fields' => [
        'name' => 'Họ và tên',
        'email' => 'Email',
        'current_password' => 'Mật khẩu hiện tại',
        'new_password' => 'Mật khẩu mới',
        'confirm_password' => 'Xác nhận mật khẩu mới',
    ],

    'messages' => [
        'profile_updated' => 'Đã cập nhật thông tin tài khoản.',
        'password_updated' => 'Đã đổi mật khẩu thành công.',
    ],

    'mail' => [
        'password_changed' => [
            'subject' => 'Mật khẩu quản trị đã được thay đổi',
            'line' => 'Mật khẩu tài khoản quản trị của bạn vừa được thay đổi lúc :time.',
            'warning' => 'Nếu không phải bạn thực hiện, vui lòng liên hệ Quản trị cấp cao ngay.',
        ],
        'email_changed' => [
            'subject' => 'Email quản trị của bạn đã được thay đổi',
            'line' => 'Email đăng nhập cho tài khoản quản trị của bạn đã được đổi thành :new_email lúc :time.',
            'warning' => 'Nếu không phải bạn thực hiện, vui lòng liên hệ Quản trị cấp cao ngay.',
        ],
    ],
];
