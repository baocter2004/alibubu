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
        'password_changed' => [
            'subject' => 'Your password has been changed',
            'line' => 'The password for your Alibubu account was changed at :time.',
            'warning' => 'If you did not make this change, please reset your password immediately and contact support.',
        ],
        'google_linked' => [
            'subject' => 'Your Google account has been linked',
            'line' => 'Your Google account was linked to this Alibubu account at :time.',
            'warning' => 'If you did not do this, please change your password immediately.',
        ],
        'email_changed' => [
            'subject' => 'Your account email has been changed',
            'line' => 'The sign-in email for your Alibubu account was changed to :new_email at :time.',
            'warning' => 'If you did not make this change, please contact support immediately.',
        ],
    ],

    'messages' => [
        'registered' => 'Registration successful! Please verify your email to start ordering.',
        'register_failed' => 'Registration failed. Please try again.',
        'logged_in' => 'Signed in successfully!',
        'logged_out' => 'Signed out successfully!',
        'login_failed' => 'The email or password is incorrect.',
        'reset_link_sent' => 'If that email exists in our system, a password reset link has been sent. Please check your inbox!',
        'reset_link_failed' => 'Could not send the reset email. Please try again later.',
        'reset_success' => 'Password updated successfully. Please sign in again!',
        'reset_failed' => 'This password reset link is invalid or has expired.',
        'google_failed' => 'Could not sign in with Google. Please try again.',
        'account_locked' => 'This account is locked or invalid.',
        'account_inactive' => 'Your account is currently inactive. Please contact support.',
        'account_locked_reason' => 'Your account has been locked: :reason',
        'throttled' => 'Too many attempts. Please try again in :seconds seconds.',
        'google_link_required' => 'The email :email is already registered. Please sign in with your password to link your Google account.',
        'google_conflict' => 'This account is already linked to a different Google account.',
        'google_unverified' => 'Your Google email is not verified. Please verify your Google email first.',
        'email_already_verified' => 'This email has already been verified.',
        'verification_resent' => 'A verification email has been sent again. Please check your inbox.',
        'verification_resend_failed' => 'Could not resend the verification email. Please try again later.',
        'must_login' => 'Please sign in to continue.',
    ],
];
