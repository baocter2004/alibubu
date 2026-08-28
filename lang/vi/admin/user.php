<?php

return [
    'title' => [
        'index' => 'Quản lý người dùng',
        'trash' => 'Người dùng đã xoá',
        'create' => 'Thêm người dùng',
        'edit' => 'Chỉnh sửa người dùng',
        'show' => 'Chi tiết người dùng',
        'confirm' => 'Xác nhận thông tin người dùng',
    ],

    'subtitle' => [
        'index' => 'Quản lý toàn bộ tài khoản trong hệ thống.',
        'trash' => 'Khôi phục hoặc xoá vĩnh viễn tài khoản đã xoá.',
        'create' => 'Điền đầy đủ thông tin để tạo tài khoản mới.',
        'edit' => 'Cập nhật thông tin cho tài khoản này.',
        'show' => 'Thông tin chi tiết của tài khoản.',
        'confirm' => 'Vui lòng kiểm tra lại thông tin trước khi lưu.',
    ],

    'sections' => [
        'basic' => 'Thông tin tài khoản',
        'basic_hint' => 'Thông tin đăng nhập và liên hệ.',
        'personal' => 'Thông tin cá nhân',
        'personal_hint' => 'Thông tin hồ sơ bổ sung.',
        'address' => 'Địa chỉ nhận hàng',
        'address_hint' => 'Tối đa :max địa chỉ (:count/:max).',
        'bank' => 'Thông tin ngân hàng',
        'bank_hint' => 'Không bắt buộc.',
    ],

    'hints' => [
        'password_optional' => 'Để trống cả hai ô mật khẩu nếu không muốn thay đổi.',
        'default_address' => 'Nếu có nhiều hơn một địa chỉ, hãy chọn một địa chỉ mặc định.',
        'loading' => 'Đang tải...',
        'load_failed' => 'Không tải được dữ liệu.',
    ],

    'buttons' => [
        'create' => 'Tạo người dùng',
        'update' => 'Cập nhật người dùng',
        'back_to_list' => 'Quay lại danh sách',
    ],

    'fields' => [
        'fullname' => 'Họ và tên',
        'email' => 'Email',
        'phone_number' => 'Số điện thoại',
        'password' => 'Mật khẩu',
        'password_confirmation' => 'Xác nhận mật khẩu',
        'avatar' => 'Ảnh đại diện',
        'role' => 'Vai trò',
        'status' => 'Trạng thái',
        'gender' => 'Giới tính',
        'birthday' => 'Ngày sinh',
        'reason_lock' => 'Lý do khoá',
        'loyalty_points' => 'Điểm tích luỹ',
        'bank_name' => 'Ngân hàng',
        'user_bank_name' => 'Chủ tài khoản',
        'bank_account' => 'Số tài khoản',
        'email_verified_at' => 'Thời điểm xác minh email',
    ],

    'address' => [
        'section' => 'Địa chỉ nhận hàng',
        'title' => 'Địa chỉ ',
        'recipient' => 'Họ và tên người nhận',
        'phone_number' => 'Số điện thoại',
        'province' => 'Tỉnh/Thành phố',
        'ward' => 'Phường/Xã',
        'detail' => 'Địa chỉ chi tiết',
        'is_default' => 'Đặt làm địa chỉ mặc định',
        'select_province' => '-- Chọn tỉnh --',
        'select_ward' => '-- Chọn xã --',
        'add' => 'Thêm địa chỉ',
        'remove' => 'Xoá địa chỉ',
        'empty' => 'Chưa có địa chỉ nào.',
        'default_badge' => 'Mặc định',
    ],

    'messages' => [
        'created' => 'Thêm người dùng thành công.',
        'updated' => 'Cập nhật người dùng thành công.',
        'deleted' => 'Xoá người dùng thành công.',
        'force_deleted' => 'Đã xoá vĩnh viễn người dùng.',
        'restored' => 'Khôi phục người dùng thành công.',
        'not_found' => 'Không tìm thấy người dùng.',
    ],
];
