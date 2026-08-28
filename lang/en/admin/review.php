<?php

return [
    'title' => 'Product reviews',
    'subtitle' => 'Approve or remove reviews submitted by customers.',

    'stats' => [
        'pending' => 'Awaiting approval',
        'approved' => 'Published',
    ],

    'fields' => [
        'product' => 'Product',
        'customer' => 'Customer',
        'rating' => 'Rating',
        'review' => 'Review',
        'status' => 'Status',
        'created_at' => 'Submitted',
    ],

    'status' => [
        'pending' => 'Pending',
        'approved' => 'Published',
        'all' => 'All statuses',
    ],

    'actions' => [
        'approve' => 'Approve',
        'reject' => 'Unpublish',
    ],

    'messages' => [
        'approved' => 'Review approved and published.',
        'rejected' => 'Review unpublished.',
        'deleted' => 'Review deleted.',
    ],
];
