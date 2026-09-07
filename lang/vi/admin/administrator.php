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
        'password' => 'Mật khẩu',
        'password_confirmation' => 'Xác nhận mật khẩu',
        'created_at' => 'Ngày tạo',
    ],
    'hints' => [
        'password_create' => 'Mật khẩu phải có ít nhất 8 ký tự.',
        'password_edit' => 'Để trống nếu muốn giữ mật khẩu hiện tại.',
    ],
    'messages' => [
        'created' => 'Tạo tài khoản quản trị thành công.',
        'updated' => 'Cập nhật tài khoản quản trị thành công.',
        'deleted' => 'Xoá tài khoản quản trị thành công.',
        'cannot_delete_self' => 'Bạn không thể xoá tài khoản của chính mình.',
        'cannot_demote_self' => 'Bạn không thể tự bỏ quyền Quản trị cấp cao của mình.',
        'cannot_delete_last_super_admin' => 'Không thể xoá tài khoản Quản trị cấp cao cuối cùng.',
    ],
    'confirm' => [
        'delete_title' => 'Xoá tài khoản quản trị này?',
        'delete_text' => 'Tài khoản này sẽ mất quyền truy cập trang quản trị.',
    ],
    'empty' => 'Chưa có tài khoản quản trị nào.',
];
