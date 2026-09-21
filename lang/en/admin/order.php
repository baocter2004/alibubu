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
        'move_to' => [
            2 => 'Confirm order',
            3 => 'Start shipping',
            4 => 'Complete order',
            5 => 'Cancel order',
        ],
        'cancel_hint' => 'Tell the customer why the order was cancelled.',
        'cancel_confirm' => 'Confirm cancellation',
        'cancel_back' => 'Back',
        'update_status' => 'Update status',
        'select_status' => 'Select a new status',
        'no_transition' => 'This order has reached a final status.',
    ],

    'messages' => [
        'not_found' => 'Order not found.',
        'status_updated' => 'Order status updated successfully.',
        'invalid_transition' => 'That status change is not allowed for this order.',
        'marked_paid' => 'Order marked as paid.',
        'marked_refunded' => 'Order marked as refunded.',
        'already_paid' => 'This order is already marked as paid.',
        'cannot_mark_paid_void' => 'Cancelled or returned orders cannot be marked as paid.',
        'not_refund_pending' => 'This order has no refund pending.',
        'requires_payment' => 'This order must be paid before moving to that status.',
        'note_required' => 'Please enter a note for this action.',
    ],

    'problems' => [
        'amount_mismatch' => 'The amount received from the gateway does not match the order total.',
        'paid_after_cancel' => 'A payment arrived for an order that was already cancelled or returned.',
        'duplicate_payment' => 'A duplicate payment notification was received for an already-settled order.',
    ],

    'refund' => [
        'title' => 'Mark as refunded',
        'note' => 'Refund note',
        'reference' => 'Reference',
    ],
];
