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
        'order' => [
            'placed' => [
                'title' => 'Đơn hàng mới :code',
                'body' => ':customer · :items sản phẩm · :total',
            ],
            'cancelled_by_customer' => [
                'title' => 'Đơn :code bị khách huỷ',
                'body' => ':customer đã huỷ đơn hàng này. Lý do: :reason',
            ],
        ],
        'payment' => [
            'received' => [
                'title' => 'Đã nhận thanh toán cho :code',
                'body' => 'Đã nhận :total qua :gateway.',
            ],
            'problem' => [
                'title' => 'Sự cố thanh toán trên đơn :code',
                'body' => ':amount qua :gateway: :problem',
            ],
        ],
        'refund' => [
            'required' => [
                'title' => 'Cần hoàn tiền cho :code',
                'body' => 'Đơn :code đã chuyển sang :status khi đã thanh toán (:total). Cần xử lý hoàn tiền.',
            ],
        ],
        'product' => [
            'low_stock' => [
                'title' => 'Sắp hết hàng: :name',
                'body' => ':sku chỉ còn :stock sản phẩm.',
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
