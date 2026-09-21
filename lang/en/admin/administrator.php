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
        'is_active' => 'Status',
        'password' => 'Password',
        'password_confirmation' => 'Confirm password',
        'created_at' => 'Created at',
    ],
    'hints' => [
        'password_create' => 'Use at least 12 characters with upper case, lower case letters and digits.',
        'password_edit' => 'Leave blank to keep the current password. Changing the password, role or status signs this account out everywhere.',
        'is_active' => 'A deactivated account is signed out immediately and cannot sign in to the back office.',
        'self_locked' => 'You cannot change the role or status of your own account.',
        'staff_only' => 'You can only create and manage Staff accounts.',
    ],
    'messages' => [
        'created' => 'Admin account created successfully.',
        'updated' => 'Admin account updated successfully.',
        'deleted' => 'Admin account removed successfully.',
        'cannot_delete_self' => 'You cannot remove your own account.',
        'cannot_demote_self' => 'You cannot remove Super Admin access from your own account.',
        'cannot_delete_last_super_admin' => 'The last Super Admin account cannot be removed.',
        'cannot_remove_last_super_admin' => 'At least one active Super Admin account must remain.',
        'cannot_deactivate_self' => 'You cannot deactivate your own account.',
        'staff_only' => 'You can only manage Staff accounts.',
        'account_inactive' => 'Your admin account has been deactivated. Please contact a Super Admin.',
    ],
    'confirm' => [
        'delete_title' => 'Remove this admin account?',
        'delete_text' => 'This account will lose access to the admin panel.',
    ],
    'status' => [
        'active' => 'Active',
        'inactive' => 'Deactivated',
    ],
    'you' => 'You',
    'empty' => 'No admin accounts found.',
];
