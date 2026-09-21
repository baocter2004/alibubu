<?php

return [
    'title' => 'Đánh giá sản phẩm',
    'subtitle' => 'Duyệt hoặc gỡ các đánh giá do khách hàng gửi.',

    'stats' => [
        'pending' => 'Chờ duyệt',
        'approved' => 'Đã hiển thị',
        'rejected' => 'Đã từ chối',
    ],

    'fields' => [
        'product' => 'Sản phẩm',
        'customer' => 'Khách hàng',
        'rating' => 'Điểm',
        'review' => 'Nội dung',
        'status' => 'Trạng thái',
        'created_at' => 'Thời gian gửi',
        'reason' => 'Lý do',
        'reason_placeholder' => 'Lý do (không bắt buộc, sẽ gửi cho khách hàng)',
    ],

    'status' => [
        'pending' => 'Chờ duyệt',
        'approved' => 'Đã hiển thị',
        'rejected' => 'Đã từ chối',
        'all' => 'Tất cả trạng thái',
    ],

    'actions' => [
        'approve' => 'Duyệt',
        'reject' => 'Gỡ hiển thị',
        'decline' => 'Từ chối',
        'confirm_reject' => 'Xác nhận',
    ],

    'notify_hint' => 'Khách hàng sẽ nhận thông báo khi đánh giá được duyệt hoặc bị từ chối.',

    'messages' => [
        'approved' => 'Đã duyệt và hiển thị đánh giá.',
        'rejected' => 'Đã gỡ đánh giá khỏi trang sản phẩm.',
        'deleted' => 'Đã xoá đánh giá.',
        'not_found' => 'Không tìm thấy đánh giá.',
    ],
];
