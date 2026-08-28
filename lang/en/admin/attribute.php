<?php

return [
    'title' => [
        'index' => 'Product attributes',
        'trash' => 'Deleted attributes',
        'create' => 'Create attribute',
        'edit' => 'Edit attribute',
        'show' => 'Attribute detail',
        'confirm' => 'Confirm attribute',
    ],

    'subtitle' => [
        'index' => 'Attributes and their values power product variants.',
        'trash' => 'Restore or permanently remove deleted attributes.',
        'create' => 'Fill in the details to create a new attribute.',
        'edit' => 'Update this attribute and its values.',
        'show' => 'Full details for this attribute.',
        'confirm' => 'Please review the information before saving.',
    ],

    'fields' => [
        'name' => 'Attribute name',
        'slug' => 'Slug',
        'is_active' => 'Status',
        'values' => 'Values',
        'value' => 'Value',
        'values_count' => 'Values',
        'value_number' => 'Value :number',
    ],

    'hints' => [
        'slug' => 'Leave blank to generate automatically from the name.',
        'values' => 'Add every option customers can pick, e.g. 128GB, 256GB.',
    ],

    'value_section' => [
        'title' => 'Attribute values',
        'add' => 'Add value',
        'remove' => 'Remove value',
        'empty' => 'No value yet. Add at least one.',
    ],

    'messages' => [
        'created' => 'Attribute created successfully.',
        'updated' => 'Attribute updated successfully.',
        'deleted' => 'Attribute deleted successfully.',
        'force_deleted' => 'Attribute permanently deleted.',
        'restored' => 'Attribute restored successfully.',
        'not_found' => 'Attribute not found.',
        'in_use' => 'This attribute is used by product variants and cannot be deleted.',
        'duplicate_value' => 'This value is already listed for this attribute.',
        'value_in_use' => 'Values already used by a variant are kept.',
    ],
];
