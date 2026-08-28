<?php

return [
    'title' => [
        'index' => 'User management',
        'trash' => 'Deleted users',
        'create' => 'Create user',
        'edit' => 'Edit user',
        'show' => 'User detail',
        'confirm' => 'Confirm user information',
    ],

    'subtitle' => [
        'index' => 'Manage every account in the system.',
        'trash' => 'Restore or permanently remove deleted accounts.',
        'create' => 'Fill in the details to create a new account.',
        'edit' => 'Update the details of this account.',
        'show' => 'Full details for this account.',
        'confirm' => 'Please review the information before saving.',
    ],

    'sections' => [
        'basic' => 'Account information',
        'basic_hint' => 'Sign-in credentials and contact details.',
        'personal' => 'Personal information',
        'personal_hint' => 'Additional profile details.',
        'address' => 'Delivery addresses',
        'address_hint' => 'Up to :max addresses (:count/:max).',
        'bank' => 'Bank information',
        'bank_hint' => 'Optional payout details.',
    ],

    'hints' => [
        'password_optional' => 'Leave both password fields empty to keep the current password.',
        'default_address' => 'With more than one address, mark one as the default delivery address.',
        'loading' => 'Loading...',
        'load_failed' => 'Could not load data.',
    ],

    'buttons' => [
        'create' => 'Create user',
        'update' => 'Update user',
        'back_to_list' => 'Back to list',
    ],

    'fields' => [
        'fullname' => 'Full name',
        'email' => 'Email',
        'phone_number' => 'Phone number',
        'password' => 'Password',
        'password_confirmation' => 'Confirm password',
        'avatar' => 'Avatar',
        'role' => 'Role',
        'status' => 'Status',
        'gender' => 'Gender',
        'birthday' => 'Date of birth',
        'reason_lock' => 'Lock reason',
        'loyalty_points' => 'Loyalty points',
        'bank_name' => 'Bank',
        'user_bank_name' => 'Account holder',
        'bank_account' => 'Account number',
        'email_verified_at' => 'Email verified at',
    ],

    'address' => [
        'section' => 'Delivery addresses',
        'title' => 'Address ',
        'recipient' => 'Recipient name',
        'phone_number' => 'Phone number',
        'province' => 'Province / City',
        'ward' => 'Ward / Commune',
        'detail' => 'Street address',
        'is_default' => 'Set as default address',
        'select_province' => '-- Select province --',
        'select_ward' => '-- Select ward --',
        'add' => 'Add address',
        'remove' => 'Remove address',
        'empty' => 'No address yet.',
        'default_badge' => 'Default',
    ],

    'messages' => [
        'created' => 'User created successfully.',
        'updated' => 'User updated successfully.',
        'deleted' => 'User deleted successfully.',
        'force_deleted' => 'User permanently deleted.',
        'restored' => 'User restored successfully.',
        'not_found' => 'User not found.',
    ],
];
