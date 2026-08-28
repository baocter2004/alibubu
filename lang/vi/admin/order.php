<?php

return [
    'title' => [
        'index' => 'Quản lý đơn hàng',
        'show' => 'Chi tiết đơn hàng',
    ],

    'subtitle' => [
        'index' => 'Theo dõi và xử lý toàn bộ đơn hàng của khách.',
    ],

    'stats' => [
        'total' => 'Tất cả đơn',
        'pending' => 'Chờ xác nhận',
        'confirmed' => 'Đã xác nhận',
        'shipping' => 'Đang giao',
        'completed' => 'Hoàn thành',
        'cancelled' => 'Đã huỷ',
    ],

    'fields' => [
        'code' => 'Mã đơn hàng',
        'customer' => 'Khách hàng',
        'fullname' => 'Người nhận',
        'phone_number' => 'Số điện thoại',
        'email' => 'Email',
        'address' => 'Địa chỉ nhận hàng',
        'note' => 'Ghi chú',
        'total_amount' => 'Tổng tiền',
        'items_count' => 'Số dòng hàng',
        'status' => 'Trạng thái',
        'payment' => 'Thanh toán',
        'cancel_reason' => 'Lý do huỷ',
        'from_date' => 'Từ ngày',
        'to_date' => 'Đến ngày',
        'confirmed_at' => 'Thời điểm xác nhận',
        'completed_at' => 'Thời điểm hoàn thành',
        'cancelled_at' => 'Thời điểm huỷ',
    ],

    'sections' => [
        'customer' => 'Thông tin khách hàng',
        'items' => 'Sản phẩm trong đơn',
        'timeline' => 'Dòng thời gian',
        'actions' => 'Cập nhật trạng thái',
    ],

    'payment' => [
        'paid' => 'Đã thanh toán',
        'unpaid' => 'Chưa thanh toán',
        'mark_paid' => 'Đánh dấu đã thanh toán',
    ],

    'item' => [
        'product' => 'Sản phẩm',
        'variant' => 'Phiên bản',
        'price' => 'Đơn giá',
        'quantity' => 'SL',
        'subtotal' => 'Thành tiền',
        'guest' => 'Khách vãng lai',
    ],

    'actions' => [
        'update_status' => 'Cập nhật trạng thái',
        'select_status' => 'Chọn trạng thái mới',
        'no_transition' => 'Đơn hàng đã ở trạng thái cuối.',
    ],

    'messages' => [
        'not_found' => 'Không tìm thấy đơn hàng.',
        'status_updated' => 'Cập nhật trạng thái đơn hàng thành công.',
        'invalid_transition' => 'Không thể chuyển sang trạng thái này.',
        'marked_paid' => 'Đã đánh dấu đơn hàng là đã thanh toán.',
        'already_paid' => 'Đơn hàng này đã được thanh toán.',
    ],
];
