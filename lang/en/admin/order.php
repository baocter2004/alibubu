<?php

return [
    'title' => [
        'index' => 'Order management',
        'show' => 'Order detail',
    ],

    'subtitle' => [
        'index' => 'Track and process every customer order.',
    ],

    'stats' => [
        'total' => 'All orders',
        'pending' => 'Pending',
        'confirmed' => 'Confirmed',
        'shipping' => 'Shipping',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ],

    'fields' => [
        'code' => 'Order code',
        'customer' => 'Customer',
        'fullname' => 'Recipient',
        'phone_number' => 'Phone number',
        'email' => 'Email',
        'address' => 'Delivery address',
        'note' => 'Note',
        'total_amount' => 'Total amount',
        'items_count' => 'Items',
        'status' => 'Status',
        'payment' => 'Payment',
        'cancel_reason' => 'Cancellation reason',
        'from_date' => 'From date',
        'to_date' => 'To date',
        'confirmed_at' => 'Confirmed at',
        'completed_at' => 'Completed at',
        'cancelled_at' => 'Cancelled at',
    ],

    'sections' => [
        'customer' => 'Customer information',
        'items' => 'Order items',
        'timeline' => 'Timeline',
        'actions' => 'Update status',
    ],

    'payment' => [
        'paid' => 'Paid',
        'unpaid' => 'Unpaid',
        'mark_paid' => 'Mark as paid',
    ],

    'item' => [
        'product' => 'Product',
        'variant' => 'Variant',
        'price' => 'Unit price',
        'quantity' => 'Qty',
        'subtotal' => 'Subtotal',
        'guest' => 'Guest',
    ],

    'actions' => [
        'update_status' => 'Update status',
        'select_status' => 'Select a new status',
        'no_transition' => 'This order has reached a final status.',
    ],

    'messages' => [
        'not_found' => 'Order not found.',
        'status_updated' => 'Order status updated successfully.',
        'invalid_transition' => 'That status change is not allowed for this order.',
        'marked_paid' => 'Order marked as paid.',
        'already_paid' => 'This order is already marked as paid.',
    ],
];
