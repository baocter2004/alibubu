<?php

return [
    'title' => 'Notifications',
    'subtitle' => 'The latest events from your store.',
    'empty' => 'No notifications yet.',
    'unread' => 'Unread',
    'mark_all_read' => 'Mark all as read',
    'mark_read' => 'Mark as read',
    'view_all' => 'View all notifications',
    'latest' => 'Latest notifications',
    'open' => 'Open',
    'delete_read' => 'Delete read notifications',
    'delete_read_confirm' => 'Delete every read notification? This cannot be undone.',
    'unread_count' => ':count unread notifications',
    'reason' => 'Reason: :reason',
    'filters' => [
        'all' => 'All',
        'unread' => 'Unread',
    ],
    'messages' => [
        'marked_read' => 'Notification marked as read.',
        'marked_all_read' => 'All notifications marked as read.',
        'deleted_read' => 'Deleted :count read notifications.',
        'not_found' => 'Notification not found.',
    ],
    'mail_common' => [
        'greeting' => 'Hello :name,',
        'action' => 'View details',
    ],
    'types' => [
        'default' => [
            'title' => 'New notification',
        ],
        'question' => [
            'asked' => [
                'title' => 'New question about :product',
                'body' => ':customer asked: “:question”',
            ],
        ],
        'review' => [
            'submitted' => [
                'title' => 'New review awaiting approval: :product',
                'body' => ':customer rated it :rating/5 and is waiting for moderation.',
            ],
        ],
        'order' => [
            'placed' => [
                'title' => 'New order :code',
                'body' => ':customer · :items items · :total',
            ],
            'cancelled_by_customer' => [
                'title' => 'Order :code cancelled by customer',
                'body' => ':customer cancelled this order. Reason: :reason',
            ],
        ],
        'payment' => [
            'received' => [
                'title' => 'Payment received for :code',
                'body' => ':total received via :gateway.',
            ],
            'problem' => [
                'title' => 'Payment problem on order :code',
                'body' => ':amount via :gateway: :problem',
            ],
        ],
        'refund' => [
            'required' => [
                'title' => 'Refund required for :code',
                'body' => 'Order :code moved to :status while paid (:total). A refund is required.',
            ],
        ],
        'product' => [
            'low_stock' => [
                'title' => 'Low stock: :name',
                'body' => ':sku only has :stock units left.',
            ],
        ],
    ],
    'question' => [
        'asked' => 'New question about :product',
    ],
    'order' => [
        'placed' => 'New order :code',
        'detail' => ':customer · :items items · :total',
    ],
    'mail' => [
        'subject' => 'New order :code',
        'greeting' => 'You have a new order!',
        'intro' => 'Order :code was placed by :customer.',
        'total' => 'Order total: :total',
        'action' => 'View order',
    ],
];
