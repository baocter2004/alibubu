<?php

return [
    'login' => [
        'title' => 'Sign in',
        'heading' => 'Welcome back',
        'subheading' => 'Enter your details to access your account.',
        'email' => 'Email',
        'password' => 'Password',
        'remember' => 'Remember me',
        'forgot' => 'Forgot password?',
        'submit' => 'Sign in',
        'no_account' => "Don't have an account?",
        'register_link' => 'Sign up',
        'google' => 'Continue with Google',
    ],

    'register' => [
        'title' => 'Create account',
        'heading' => 'Create your account',
        'subheading' => 'Join Alibubu and start shopping today.',
        'fullname' => 'Full name',
        'email' => 'Email',
        'password' => 'Password',
        'password_confirmation' => 'Confirm password',
        'submit' => 'Sign up',
        'have_account' => 'Already have an account?',
        'login_link' => 'Sign in',
    ],

    'forgot' => [
        'title' => 'Forgot password',
        'heading' => 'Forgot your password?',
        'subheading' => 'Enter your registered email and we will send you a reset link.',
        'submit' => 'Send reset link',
        'remembered' => 'Remembered your password?',
        'login_link' => 'Sign in',
    ],

    'reset' => [
        'title' => 'Reset password',
        'heading' => 'Reset your password',
        'subheading' => 'Choose a new password for your account.',
        'password' => 'New password',
        'password_confirmation' => 'Confirm password',
        'submit' => 'Update password',
    ],

    'mail' => [
        'verify' => [
            'title' => 'Verify your email',
            'greeting' => 'Hello :name,',
            'intro' => 'Tap the button below to verify your account:',
            'action' => 'Verify email',
            'expires' => 'This link expires in :minutes minutes.',
            'ignore' => 'If you did not create an account, no further action is required.',
        ],
    ],

    'messages' => [
        'registered' => 'Registration successful! Please verify your email to start ordering.',
        'register_failed' => 'Registration failed. Please try again.',
        'logged_in' => 'Signed in successfully!',
        'logged_out' => 'Signed out successfully!',
        'login_failed' => 'The email or password is incorrect.',
        'reset_link_sent' => 'A password reset link has been sent. Please check your inbox!',
        'reset_link_failed' => 'Could not send the reset email. Please try again later.',
        'reset_success' => 'Password updated successfully. Please sign in again!',
        'reset_failed' => 'This password reset link is invalid or has expired.',
        'google_failed' => 'Could not sign in with Google. Please try again.',
        'account_locked' => 'This account is locked or invalid.',
        'email_already_verified' => 'This email has already been verified.',
        'must_login' => 'Please sign in to continue.',
    ],
];
