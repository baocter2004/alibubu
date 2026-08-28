<?php

return [
    'title' => [
        'index' => 'Quản lý danh mục',
        'trash' => 'Danh mục đã xoá',
        'create' => 'Thêm danh mục',
        'edit' => 'Chỉnh sửa danh mục',
        'show' => 'Chi tiết danh mục',
        'confirm' => 'Xác nhận thông tin danh mục',
    ],

    'subtitle' => [
        'index' => 'Sắp xếp các danh mục chứa sản phẩm của bạn.',
        'trash' => 'Khôi phục hoặc xoá vĩnh viễn danh mục đã xoá.',
        'confirm' => 'Vui lòng kiểm tra lại thông tin trước khi lưu.',
    ],

    'fields' => [
        'name' => 'Tên danh mục',
        'slug' => 'Đường dẫn',
        'icon' => 'Lớp biểu tượng',
        'parent' => 'Danh mục cha',
        'ordinal' => 'Thứ tự hiển thị',
        'is_active' => 'Trạng thái',
        'products_count' => 'Sản phẩm',
        'children_count' => 'Danh mục con',
    ],

    'hints' => [
        'slug' => 'Để trống để tự sinh từ tên danh mục.',
        'icon' => 'Lớp Font Awesome, ví dụ fa-solid fa-mobile-screen.',
        'parent' => 'Để trống nếu đây là danh mục gốc.',
    ],

    'no_parent' => 'Không có danh mục cha',

    'messages' => [
        'created' => 'Thêm danh mục thành công.',
        'updated' => 'Cập nhật danh mục thành công.',
        'deleted' => 'Xoá danh mục thành công.',
        'force_deleted' => 'Đã xoá vĩnh viễn danh mục.',
        'restored' => 'Khôi phục danh mục thành công.',
        'not_found' => 'Không tìm thấy danh mục.',
        'has_products' => 'Danh mục vẫn còn sản phẩm nên không thể xoá.',
        'has_children' => 'Danh mục vẫn còn danh mục con nên không thể xoá.',
    ],
];
