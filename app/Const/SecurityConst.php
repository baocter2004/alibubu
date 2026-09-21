<?php

namespace App\Const;

class SecurityConst
{
    const LIMITER_LOGIN = 'login';
    const LIMITER_REGISTER = 'register';
    const LIMITER_PASSWORD_EMAIL = 'password-email';
    const LIMITER_PASSWORD_RESET = 'password-reset';
    const LIMITER_VERIFICATION = 'verification';
    const LIMITER_ADMIN_LOGIN = 'admin-login';
    const LIMITER_ADMIN_PASSWORD = 'admin-password';
    const LIMITER_API = 'api';

    const LOGIN_PER_EMAIL_PER_MINUTE = 5;
    const LOGIN_PER_IP_PER_MINUTE = 20;
    const REGISTER_PER_IP_PER_MINUTE = 5;
    const REGISTER_PER_IP_PER_HOUR = 20;
    const PASSWORD_EMAIL_PER_EMAIL_PER_MINUTE = 3;
    const PASSWORD_EMAIL_PER_IP_PER_MINUTE = 10;
    const PASSWORD_RESET_PER_EMAIL_PER_MINUTE = 5;
    const PASSWORD_RESET_PER_IP_PER_MINUTE = 10;
    const VERIFICATION_PER_MINUTE = 2;
    const VERIFICATION_PER_HOUR = 10;
    const ADMIN_LOGIN_PER_EMAIL_PER_MINUTE = 5;
    const ADMIN_LOGIN_PER_IP_PER_MINUTE = 20;
    const ADMIN_LOGIN_PER_ACCOUNT_PER_HOUR = 30;
    const ADMIN_PASSWORD_PER_EMAIL_PER_MINUTE = 3;
    const ADMIN_PASSWORD_PER_IP_PER_MINUTE = 10;
    const API_PER_MINUTE = 60;

    const SESSION_GOOGLE_LINK = 'auth.google_link';
    const SESSION_PASSWORD_HASH_PREFIX = 'password_hash_';
    const NOTICE_TIME_FORMAT = 'H:i d/m/Y';
    const GOOGLE_LINK_TTL_MINUTES = 10;

    const GOOGLE_LOGGED_IN = 'logged_in';
    const GOOGLE_LINKED = 'linked';
    const GOOGLE_CREATED = 'created';
    const GOOGLE_INACTIVE = 'inactive';
    const GOOGLE_LINK_REQUIRED = 'link_required';
    const GOOGLE_CONFLICT = 'conflict';
    const GOOGLE_UNVERIFIED = 'unverified';
    const GOOGLE_FAILED = 'failed';

    const LOGIN_OK = 'ok';
    const LOGIN_FAILED = 'failed';
    const LOGIN_INACTIVE = 'inactive';

    const SENSITIVE_INPUTS = ['password', 'password_confirmation', 'current_password', 'profile_password', 'token'];

    const HEADER_FRAME_OPTIONS = 'DENY';
    const HEADER_CONTENT_TYPE_OPTIONS = 'nosniff';
    const HEADER_REFERRER_POLICY = 'strict-origin-when-cross-origin';
    const HEADER_PERMISSIONS_POLICY = 'camera=(), microphone=(), geolocation=(), usb=(), payment=()';
    const HEADER_CONTENT_SECURITY_POLICY = "frame-ancestors 'none'";
    const HEADER_HSTS = 'max-age=31536000; includeSubDomains';
}
