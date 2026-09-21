<?php

return [
    'title' => [
        'index' => 'Tài khoản quản trị',
        'create' => 'Thêm tài khoản quản trị',
        'edit' => 'Sửa tài khoản quản trị',
    ],
    'subtitle' => [
        'index' => 'Quản lý quyền truy cập và trách nhiệm trong trang quản trị.',
        'create' => 'Tạo tài khoản bảo mật cho thành viên trong đội ngũ.',
        'edit' => 'Cập nhật thông tin và cấp độ truy cập.',
    ],
    'fields' => [
        'name' => 'Họ và tên',
        'email' => 'Email',
        'role' => 'Vai trò',
        'is_active' => 'Trạng thái',
        'password' => 'Mật khẩu',
        'password_confirmation' => 'Xác nhận mật khẩu',
        'created_at' => 'Ngày tạo',
    ],
    'hints' => [
        'password_create' => 'Mật khẩu có ít nhất 12 ký tự, gồm chữ hoa, chữ thường và chữ số.',
        'password_edit' => 'Để trống nếu muốn giữ mật khẩu hiện tại. Đổi mật khẩu, vai trò hoặc trạng thái sẽ đăng xuất tài khoản này khỏi mọi thiết bị.',
        'is_active' => 'Tài khoản bị khoá sẽ bị đăng xuất ngay và không thể đăng nhập trang quản trị.',
        'self_locked' => 'Bạn không thể tự đổi vai trò hoặc khoá tài khoản của chính mình.',
        'staff_only' => 'Bạn chỉ có thể tạo và quản lý tài khoản Nhân viên.',
    ],
    'messages' => [
        'created' => 'Tạo tài khoản quản trị thành công.',
        'updated' => 'Cập nhật tài khoản quản trị thành công.',
        'deleted' => 'Xoá tài khoản quản trị thành công.',
        'cannot_delete_self' => 'Bạn không thể xoá tài khoản của chính mình.',
        'cannot_demote_self' => 'Bạn không thể tự bỏ quyền Quản trị cấp cao của mình.',
        'cannot_delete_last_super_admin' => 'Không thể xoá tài khoản Quản trị cấp cao cuối cùng.',
        'cannot_remove_last_super_admin' => 'Phải còn ít nhất một tài khoản Quản trị cấp cao đang hoạt động.',
        'cannot_deactivate_self' => 'Bạn không thể tự khoá tài khoản của chính mình.',
        'staff_only' => 'Bạn chỉ được quản lý tài khoản Nhân viên.',
        'account_inactive' => 'Tài khoản quản trị của bạn đã bị khoá. Vui lòng liên hệ Quản trị cấp cao.',
    ],
    'confirm' => [
        'delete_title' => 'Xoá tài khoản quản trị này?',
        'delete_text' => 'Tài khoản này sẽ mất quyền truy cập trang quản trị.',
    ],
    'status' => [
        'active' => 'Đang hoạt động',
        'inactive' => 'Đã khoá',
    ],
    'you' => 'Bạn',
    'empty' => 'Chưa có tài khoản quản trị nào.',
];
