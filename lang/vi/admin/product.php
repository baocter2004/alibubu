<?php

return [
    'title' => [
        'index' => 'Quản lý sản phẩm',
        'trash' => 'Sản phẩm đã xoá',
        'create' => 'Thêm sản phẩm',
        'edit' => 'Chỉnh sửa sản phẩm',
        'show' => 'Chi tiết sản phẩm',
        'confirm' => 'Xác nhận thông tin sản phẩm',
    ],

    'subtitle' => [
        'index' => 'Quản lý toàn bộ sản phẩm trong hệ thống.',
        'trash' => 'Khôi phục hoặc xoá vĩnh viễn sản phẩm đã xoá.',
        'confirm' => 'Vui lòng kiểm tra lại thông tin trước khi lưu.',
    ],

    'sections' => [
        'general' => 'Thông tin chung',
        'pricing' => 'Giá bán',
        'media' => 'Hình ảnh',
        'visibility' => 'Hiển thị',
    ],

    'fields' => [
        'name' => 'Tên sản phẩm',
        'slug' => 'Đường dẫn',
        'sku' => 'Mã SKU',
        'branch' => 'Thương hiệu',
        'categories' => 'Danh mục',
        'short_descriptions' => 'Mô tả ngắn',
        'descriptions' => 'Mô tả chi tiết',
        'thumbnail' => 'Ảnh đại diện',
        'price' => 'Giá gốc',
        'sale_price' => 'Giá khuyến mãi',
        'sale_price_start_at' => 'Bắt đầu khuyến mãi',
        'sale_price_end_at' => 'Kết thúc khuyến mãi',
        'is_featured' => 'Sản phẩm nổi bật',
        'is_trending' => 'Sản phẩm thịnh hành',
        'is_active' => 'Trạng thái',
        'views' => 'Lượt xem',
        'type' => 'Loại sản phẩm',
        'variants' => 'Phiên bản',
        'attributes' => 'Thuộc tính',
        'variant_number' => 'Phiên bản :number',
        'variants_count' => 'Phiên bản',
    ],

    'hints' => [
        'slug' => 'Để trống để tự sinh từ tên sản phẩm.',
        'sale_price' => 'Phải nhỏ hơn giá gốc.',
        'thumbnail' => 'Định dạng JPG, PNG hoặc WEBP, tối đa 2MB.',
    ],

    'variant' => [
        'section' => 'Phiên bản sản phẩm',
        'description' => 'Thêm một dòng cho mỗi tổ hợp bạn bán, ví dụ 256GB / Đen.',
        'add' => 'Thêm phiên bản',
        'remove' => 'Xoá phiên bản',
        'empty' => 'Chưa có phiên bản nào. Hãy thêm ít nhất một phiên bản.',
        'select_attributes' => 'Chọn giá trị thuộc tính',
    ],

    'messages' => [
        'created' => 'Thêm sản phẩm thành công.',
        'updated' => 'Cập nhật sản phẩm thành công.',
        'deleted' => 'Xoá sản phẩm thành công.',
        'force_deleted' => 'Đã xoá vĩnh viễn sản phẩm.',
        'restored' => 'Khôi phục sản phẩm thành công.',
        'duplicate_variant' => 'Tổ hợp thuộc tính này đã được dùng cho phiên bản khác.',
        'not_found' => 'Không tìm thấy sản phẩm.',
    ],
];
