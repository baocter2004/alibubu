<?php

return [
    'title' => [
        'index' => 'Quản lý thương hiệu',
        'trash' => 'Thương hiệu đã xoá',
        'create' => 'Thêm thương hiệu',
        'edit' => 'Chỉnh sửa thương hiệu',
        'show' => 'Chi tiết thương hiệu',
        'confirm' => 'Xác nhận thông tin thương hiệu',
    ],

    'subtitle' => [
        'index' => 'Quản lý các thương hiệu cung cấp sản phẩm.',
        'trash' => 'Khôi phục hoặc xoá vĩnh viễn thương hiệu đã xoá.',
        'create' => 'Điền đầy đủ thông tin để tạo thương hiệu mới.',
        'edit' => 'Cập nhật thông tin cho thương hiệu này.',
        'show' => 'Thông tin chi tiết của thương hiệu.',
        'confirm' => 'Vui lòng kiểm tra lại thông tin trước khi lưu.',
    ],

    'fields' => [
        'name' => 'Tên thương hiệu',
        'slug' => 'Đường dẫn',
        'logo' => 'Logo',
        'is_active' => 'Trạng thái',
        'products_count' => 'Sản phẩm',
    ],

    'hints' => [
        'slug' => 'Để trống để tự sinh từ tên thương hiệu.',
        'logo' => 'Định dạng JPG hoặc PNG, tối đa 2MB.',
    ],

    'messages' => [
        'created' => 'Thêm thương hiệu thành công.',
        'updated' => 'Cập nhật thương hiệu thành công.',
        'deleted' => 'Xoá thương hiệu thành công.',
        'force_deleted' => 'Đã xoá vĩnh viễn thương hiệu.',
        'restored' => 'Khôi phục thương hiệu thành công.',
        'not_found' => 'Không tìm thấy thương hiệu.',
        'has_products' => 'Thương hiệu vẫn còn sản phẩm nên không thể xoá.',
    ],
];
