<?php

return [
    'title' => [
        'index' => 'Brand management',
        'trash' => 'Deleted brands',
        'create' => 'Create brand',
        'edit' => 'Edit brand',
        'show' => 'Brand detail',
        'confirm' => 'Confirm brand information',
    ],

    'subtitle' => [
        'index' => 'Manage the brands that supply your products.',
        'trash' => 'Restore or permanently remove deleted brands.',
        'confirm' => 'Please review the information before saving.',
    ],

    'fields' => [
        'name' => 'Brand name',
        'slug' => 'Slug',
        'logo' => 'Logo',
        'is_active' => 'Status',
        'products_count' => 'Products',
    ],

    'hints' => [
        'slug' => 'Leave blank to generate automatically from the name.',
        'logo' => 'JPG or PNG, maximum 2MB.',
    ],

    'messages' => [
        'created' => 'Brand created successfully.',
        'updated' => 'Brand updated successfully.',
        'deleted' => 'Brand deleted successfully.',
        'force_deleted' => 'Brand permanently deleted.',
        'restored' => 'Brand restored successfully.',
        'not_found' => 'Brand not found.',
        'has_products' => 'This brand still has products and cannot be deleted.',
    ],
];
