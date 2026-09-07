<?php

return [
    'title' => [
        'index' => 'Admin accounts',
        'create' => 'Create admin account',
        'edit' => 'Edit admin account',
    ],
    'subtitle' => [
        'index' => 'Manage back-office access and responsibilities.',
        'create' => 'Create a protected account for a team member.',
        'edit' => 'Update account details and access level.',
    ],
    'fields' => [
        'name' => 'Full name',
        'email' => 'Email',
        'role' => 'Role',
        'password' => 'Password',
        'password_confirmation' => 'Confirm password',
        'created_at' => 'Created at',
    ],
    'hints' => [
        'password_create' => 'Use at least 8 characters.',
        'password_edit' => 'Leave blank to keep the current password.',
    ],
    'messages' => [
        'created' => 'Admin account created successfully.',
        'updated' => 'Admin account updated successfully.',
        'deleted' => 'Admin account removed successfully.',
        'cannot_delete_self' => 'You cannot remove your own account.',
        'cannot_demote_self' => 'You cannot remove Super Admin access from your own account.',
        'cannot_delete_last_super_admin' => 'The last Super Admin account cannot be removed.',
    ],
    'confirm' => [
        'delete_title' => 'Remove this admin account?',
        'delete_text' => 'This account will lose access to the admin panel.',
    ],
    'empty' => 'No admin accounts found.',
];
