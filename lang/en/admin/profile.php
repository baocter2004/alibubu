<?php

return [
    'title' => 'My account',
    'subtitle' => 'Update the details and password of your admin account.',

    'sections' => [
        'information' => 'Account information',
        'information_hint' => 'The name and email used to sign in to the admin area.',
        'password' => 'Change password',
        'password_hint' => 'Use at least 8 characters and avoid reusing your previous password.',
    ],

    'fields' => [
        'name' => 'Full name',
        'email' => 'Email',
        'current_password' => 'Current password',
        'new_password' => 'New password',
        'confirm_password' => 'Confirm new password',
    ],

    'messages' => [
        'profile_updated' => 'Your account details have been updated.',
        'password_updated' => 'Your password has been changed.',
    ],

    'mail' => [
        'password_changed' => [
            'subject' => 'Your admin password has been changed',
            'line' => 'The password for your admin account was changed at :time.',
            'warning' => 'If you did not make this change, please contact a Super Admin immediately.',
        ],
        'email_changed' => [
            'subject' => 'Your admin email has been changed',
            'line' => 'The sign-in email for your admin account was changed to :new_email at :time.',
            'warning' => 'If you did not make this change, please contact a Super Admin immediately.',
        ],
    ],
];
