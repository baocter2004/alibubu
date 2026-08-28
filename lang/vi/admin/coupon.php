<?php

return [
    'title' => [
        'index' => 'Mã giảm giá',
        'trash' => 'Mã giảm giá đã xoá',
        'create' => 'Thêm mã giảm giá',
        'edit' => 'Chỉnh sửa mã giảm giá',
        'show' => 'Chi tiết mã giảm giá',
        'confirm' => 'Xác nhận mã giảm giá',
    ],

    'subtitle' => [
        'index' => 'Tạo và theo dõi các mã khách hàng dùng khi thanh toán.',
        'trash' => 'Khôi phục hoặc xoá vĩnh viễn mã đã xoá.',
        'create' => 'Điền đầy đủ thông tin để tạo mã giảm giá mới.',
        'edit' => 'Cập nhật thông tin cho mã giảm giá này.',
        'show' => 'Thông tin chi tiết và lượt sử dụng của mã.',
        'confirm' => 'Vui lòng kiểm tra lại thông tin trước khi lưu.',
    ],

    'sections' => [
        'general' => 'Thông tin mã',
        'discount' => 'Giá trị giảm',
        'conditions' => 'Điều kiện áp dụng',
        'schedule' => 'Thời gian hiệu lực',
    ],

    'fields' => [
        'code' => 'Mã',
        'title' => 'Tiêu đề',
        'description' => 'Mô tả',
        'discount_type' => 'Loại giảm giá',
        'discount_value' => 'Giá trị giảm',
        'usage_limit' => 'Giới hạn lượt dùng',
        'usage_count' => 'Đã dùng',
        'usage' => 'Lượt dùng',
        'is_active' => 'Trạng thái',
        'start_date' => 'Bắt đầu',
        'end_date' => 'Kết thúc',
        'min_order_value' => 'Giá trị đơn tối thiểu',
        'max_discount_value' => 'Giảm tối đa',
        'valid_categories' => 'Giới hạn theo danh mục',
        'users' => 'Khách hàng đã dùng',
    ],

    'hints' => [
        'code' => 'Chỉ gồm chữ in hoa, số, dấu gạch ngang và gạch dưới.',
        'discount_value' => 'Phần trăm (1-100) hoặc số tiền cố định tuỳ theo loại.',
        'max_discount_value' => 'Bắt buộc với mã phần trăm để giới hạn số tiền giảm.',
        'valid_categories' => 'Để trống nếu áp dụng cho mọi danh mục.',
        'schedule' => 'Để trống nếu mã không giới hạn thời gian.',
    ],

    'all_categories' => 'Tất cả danh mục',
    'unlimited' => 'Không giới hạn',

    'messages' => [
        'created' => 'Thêm mã giảm giá thành công.',
        'updated' => 'Cập nhật mã giảm giá thành công.',
        'deleted' => 'Xoá mã giảm giá thành công.',
        'force_deleted' => 'Đã xoá vĩnh viễn mã giảm giá.',
        'restored' => 'Khôi phục mã giảm giá thành công.',
        'not_found' => 'Không tìm thấy mã giảm giá.',
        'already_used' => 'Mã đã được sử dụng nên không thể xoá.',
        'code_format' => 'Mã chỉ được chứa chữ in hoa, số, dấu gạch ngang và gạch dưới.',
    ],
];
