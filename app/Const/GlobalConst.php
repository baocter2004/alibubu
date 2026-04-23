<?php

namespace App\Const;

class GlobalConst
{
    const IS_NOT_ACTIVE = 0;
    const IS_ACTIVE = 1;
    const STATUS = [
        self::IS_NOT_ACTIVE => "Không hoạt động",
        self::IS_ACTIVE => "Hoạt động"
    ];

    const LIMIT = 10;
}
