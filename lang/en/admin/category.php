<?php

return [
    'title' => [
        'index' => 'Category management',
        'trash' => 'Deleted categories',
        'create' => 'Create category',
        'edit' => 'Edit category',
        'show' => 'Category detail',
        'confirm' => 'Confirm category information',
    ],

    'subtitle' => [
        'index' => 'Organise the categories your products belong to.',
        'trash' => 'Restore or permanently remove deleted categories.',
        'confirm' => 'Please review the information before saving.',
    ],

    'fields' => [
        'name' => 'Category name',
        'slug' => 'Slug',
        'icon' => 'Icon class',
        'parent' => 'Parent category',
        'ordinal' => 'Display order',
        'is_active' => 'Status',
        'products_count' => 'Products',
        'children_count' => 'Subcategories',
    ],

    'hints' => [
        'slug' => 'Leave blank to generate automatically from the name.',
        'icon' => 'A Font Awesome class, e.g. fa-solid fa-mobile-screen.',
        'parent' => 'Leave blank to create a top-level category.',
    ],

    'no_parent' => 'No parent category',

    'messages' => [
        'created' => 'Category created successfully.',
        'updated' => 'Category updated successfully.',
        'deleted' => 'Category deleted successfully.',
        'force_deleted' => 'Category permanently deleted.',
        'restored' => 'Category restored successfully.',
        'not_found' => 'Category not found.',
        'has_products' => 'This category still has products and cannot be deleted.',
        'has_children' => 'This category still has subcategories and cannot be deleted.',
    ],
];
