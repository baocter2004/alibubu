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
];
