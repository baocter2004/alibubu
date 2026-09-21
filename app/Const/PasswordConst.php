<?php

namespace App\Const;

use Illuminate\Validation\Rules\Password;

class PasswordConst
{
    const CUSTOMER_MIN_LENGTH = 8;
    const ADMIN_MIN_LENGTH = 12;
    const MAX_LENGTH = 72;

    public static function customer(): Password
    {
        return Password::min(self::CUSTOMER_MIN_LENGTH)->max(self::MAX_LENGTH)->letters()->numbers();
    }

    public static function admin(): Password
    {
        return Password::min(self::ADMIN_MIN_LENGTH)->max(self::MAX_LENGTH)->mixedCase()->numbers();
    }
}
