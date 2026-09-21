<?php

return [
    'title' => 'Product reviews',
    'subtitle' => 'Approve or remove reviews submitted by customers.',

    'stats' => [
        'pending' => 'Awaiting approval',
        'approved' => 'Published',
        'rejected' => 'Rejected',
    ],

    'fields' => [
        'product' => 'Product',
        'customer' => 'Customer',
        'rating' => 'Rating',
        'review' => 'Review',
        'status' => 'Status',
        'created_at' => 'Submitted',
        'reason' => 'Reason',
        'reason_placeholder' => 'Reason (optional, sent to the customer)',
    ],

    'status' => [
        'pending' => 'Pending',
        'approved' => 'Published',
        'rejected' => 'Rejected',
        'all' => 'All statuses',
    ],

    'actions' => [
        'approve' => 'Approve',
        'reject' => 'Unpublish',
        'decline' => 'Reject',
        'confirm_reject' => 'Confirm',
    ],

    'notify_hint' => 'The customer is notified when a review is approved or rejected.',

    'messages' => [
        'approved' => 'Review approved and published.',
        'rejected' => 'Review unpublished.',
        'deleted' => 'Review deleted.',
        'not_found' => 'Review not found.',
    ],
];
