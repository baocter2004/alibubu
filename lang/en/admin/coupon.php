<?php

return [
    'title' => [
        'index' => 'Discount codes',
        'trash' => 'Deleted discount codes',
        'create' => 'Create discount code',
        'edit' => 'Edit discount code',
        'show' => 'Discount code detail',
        'confirm' => 'Confirm discount code',
    ],

    'subtitle' => [
        'index' => 'Create and track the codes customers use at checkout.',
        'trash' => 'Restore or permanently remove deleted codes.',
        'create' => 'Fill in the details to create a new discount code.',
        'edit' => 'Update the details of this discount code.',
        'show' => 'Full details and usage for this code.',
        'confirm' => 'Please review the information before saving.',
    ],

    'sections' => [
        'general' => 'Code details',
        'discount' => 'Discount value',
        'conditions' => 'Conditions',
        'schedule' => 'Schedule',
    ],

    'fields' => [
        'code' => 'Code',
        'title' => 'Title',
        'description' => 'Description',
        'discount_type' => 'Discount type',
        'discount_value' => 'Discount value',
        'usage_limit' => 'Usage limit',
        'usage_count' => 'Times used',
        'usage' => 'Usage',
        'is_active' => 'Status',
        'start_date' => 'Starts at',
        'end_date' => 'Ends at',
        'min_order_value' => 'Minimum order value',
        'max_discount_value' => 'Maximum discount',
        'valid_categories' => 'Limit to categories',
        'users' => 'Customers who used it',
    ],

    'hints' => [
        'code' => 'Uppercase letters, numbers, hyphen and underscore only.',
        'discount_value' => 'A percentage (1-100) or a fixed amount depending on the type.',
        'max_discount_value' => 'Required for percentage codes to cap the discount.',
        'valid_categories' => 'Leave empty to allow every category.',
        'schedule' => 'Leave empty for a code that never expires.',
    ],

    'all_categories' => 'All categories',
    'unlimited' => 'Unlimited',

    'messages' => [
        'created' => 'Discount code created successfully.',
        'updated' => 'Discount code updated successfully.',
        'deleted' => 'Discount code deleted successfully.',
        'force_deleted' => 'Discount code permanently deleted.',
        'restored' => 'Discount code restored successfully.',
        'not_found' => 'Discount code not found.',
        'already_used' => 'This code has already been used and cannot be deleted.',
        'code_format' => 'The code may only contain uppercase letters, numbers, hyphen and underscore.',
    ],
];
