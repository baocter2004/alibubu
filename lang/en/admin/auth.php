<?php

return [
    'login' => [
        'title' => 'Administrator sign in',
        'heading' => 'Welcome back',
        'subheading' => 'Sign in to access the administration dashboard.',
        'email' => 'Email',
        'password' => 'Password',
        'remember' => 'Remember me',
        'forgot' => 'Forgot your password?',
        'submit' => 'Sign in',
    ],

    'forgot' => [
        'title' => 'Forgot password',
        'heading' => 'Forgot your password?',
        'subheading' => 'Enter your administrator email and we will send you a reset link.',
        'submit' => 'Send reset link',
        'back' => 'Back to sign in',
    ],

    'reset' => [
        'title' => 'Reset password',
        'heading' => 'Reset your password',
        'subheading' => 'Choose a new password for your administrator account.',
        'password' => 'New password',
        'password_confirmation' => 'Confirm password',
        'submit' => 'Update password',
    ],

    'messages' => [
        'logged_in' => 'Signed in successfully!',
        'logged_out' => 'Signed out successfully!',
        'failed' => 'The email or password is incorrect.',
        'reset_link_sent' => 'A password reset link has been sent. Please check your email.',
        'reset_link_failed' => 'Could not send the reset email. Please try again later.',
        'reset_success' => 'Password updated successfully. Please sign in again!',
        'reset_failed' => 'This password reset link is invalid or has expired.',
        'forbidden' => 'You do not have permission to access this area.',
    ],
];
