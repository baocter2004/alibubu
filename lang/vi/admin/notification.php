<?php

return [
    'title' => 'Thông báo',
    'subtitle' => 'Các sự kiện mới nhất của cửa hàng.',
    'empty' => 'Chưa có thông báo nào.',
    'unread' => 'Chưa đọc',
    'mark_all_read' => 'Đánh dấu đã đọc tất cả',
    'mark_read' => 'Đánh dấu đã đọc',
    'view_all' => 'Xem tất cả thông báo',
    'latest' => 'Thông báo mới nhất',
    'open' => 'Mở',
    'delete_read' => 'Xoá thông báo đã đọc',
    'delete_read_confirm' => 'Xoá toàn bộ thông báo đã đọc? Thao tác này không thể hoàn tác.',
    'unread_count' => ':count thông báo chưa đọc',
    'reason' => 'Lý do: :reason',
    'filters' => [
        'all' => 'Tất cả',
        'unread' => 'Chưa đọc',
    ],
    'messages' => [
        'marked_read' => 'Đã đánh dấu thông báo là đã đọc.',
        'marked_all_read' => 'Đã đánh dấu tất cả thông báo là đã đọc.',
        'deleted_read' => 'Đã xoá :count thông báo đã đọc.',
        'not_found' => 'Không tìm thấy thông báo.',
    ],
    'mail_common' => [
        'greeting' => 'Xin chào :name,',
        'action' => 'Xem chi tiết',
    ],
    'types' => [
        'default' => [
            'title' => 'Thông báo mới',
        ],
        'question' => [
            'asked' => [
                'title' => 'Câu hỏi mới về :product',
                'body' => ':customer hỏi: “:question”',
            ],
        ],
        'review' => [
            'submitted' => [
                'title' => 'Đánh giá mới chờ duyệt: :product',
                'body' => ':customer đã đánh giá :rating/5 sao và đang chờ duyệt.',
            ],
        ],
    ],
    'question' => [
        'asked' => 'Câu hỏi mới về :product',
    ],
    'order' => [
        'placed' => 'Đơn hàng mới :code',
        'detail' => ':customer · :items sản phẩm · :total',
    ],
    'mail' => [
        'subject' => 'Đơn hàng mới :code',
        'greeting' => 'Có đơn hàng mới!',
        'intro' => 'Đơn :code vừa được đặt bởi :customer.',
        'total' => 'Tổng thanh toán: :total',
        'action' => 'Xem đơn hàng',
    ],
];
