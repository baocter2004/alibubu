<?php

return [
    'title' => [
        'index' => 'Product management',
        'trash' => 'Deleted products',
        'create' => 'Create product',
        'edit' => 'Edit product',
        'show' => 'Product detail',
        'confirm' => 'Confirm product information',
    ],

    'subtitle' => [
        'index' => 'Manage every product in your catalog.',
        'trash' => 'Restore or permanently remove deleted products.',
        'confirm' => 'Please review the information before saving.',
    ],

    'sections' => [
        'general' => 'General information',
        'pricing' => 'Pricing',
        'media' => 'Media',
        'visibility' => 'Visibility',
    ],

    'fields' => [
        'name' => 'Product name',
        'slug' => 'Slug',
        'sku' => 'SKU',
        'branch' => 'Brand',
        'categories' => 'Categories',
        'short_descriptions' => 'Short description',
        'descriptions' => 'Full description',
        'thumbnail' => 'Thumbnail',
        'price' => 'Regular price',
        'sale_price' => 'Sale price',
        'sale_price_start_at' => 'Sale starts at',
        'sale_price_end_at' => 'Sale ends at',
        'is_featured' => 'Featured product',
        'is_trending' => 'Trending product',
        'is_active' => 'Status',
        'views' => 'Views',
        'type' => 'Product type',
        'variants' => 'Variants',
        'attributes' => 'Attributes',
        'variant_number' => 'Variant :number',
        'specifications' => 'Specifications',
        'variants_count' => 'Variants',
    ],

    'hints' => [
        'slug' => 'Leave blank to generate automatically from the name.',
        'sale_price' => 'Must be lower than the regular price.',
        'thumbnail' => 'JPG, PNG or WEBP, maximum 2MB.',
    ],

    'variant' => [
        'section' => 'Product variants',
        'description' => 'Add a row for every combination you sell, e.g. 256GB / Black.',
        'add' => 'Add variant',
        'remove' => 'Remove variant',
        'empty' => 'No variants yet. Add at least one.',
        'select_attributes' => 'Select attribute values',
    ],

    'spec' => [
        'section' => 'Technical specifications',
        'description' => 'Add the spec rows shown on the product page, e.g. Screen · 6.9 inch.',
        'add' => 'Add specification',
        'remove' => 'Remove specification',
        'empty' => 'No specification yet.',
        'group' => 'Group',
        'group_placeholder' => 'Display, Camera, Battery...',
        'name' => 'Attribute',
        'name_placeholder' => 'Screen size',
        'value' => 'Value',
        'value_placeholder' => '6.9 inch',
    ],

    'messages' => [
        'created' => 'Product created successfully.',
        'updated' => 'Product updated successfully.',
        'deleted' => 'Product deleted successfully.',
        'force_deleted' => 'Product permanently deleted.',
        'restored' => 'Product restored successfully.',
        'duplicate_variant' => 'This attribute combination is already used by another variant.',
        'not_found' => 'Product not found.',
    ],
];
