<?php

namespace App\Const;

class UserConst
{
    const ROLE_USER = 1;
    const ROLE_EMPLOYEE = 2;
    const ROLE_ADMIN = 3;


    const ROLE = [
        self::ROLE_USER => 'Người Dùng',
        self::ROLE_EMPLOYEE => 'Nhân Viên',
        self::ROLE_ADMIN => 'Quản Trị Viên'
    ];

    const MALE = 1;
    const FEMALE = 2;
    const OTHER = 3;

    const GENDER = [
        1 => 'Nam',
        2 => 'Nữ',
        3 => 'Khác'
    ];

    const NOT_SELECTED = 0;
    const YES = 1;
    const NOT = 2;

    const YES_NO_OPTIONS = [
        self::NOT_SELECTED => 'chưa chọn',
        self::YES => 'có',
        self::NOT => 'không',
    ];

    const YES_NO_ONLY = [
        self::YES => 'có',
        self::NOT => 'không',
    ];

    const FULL_TIME = 1;
    const PART_TIME = 2;
    const BOARD_MEMBER = 3;

    const EMPLOYEE_TYPES = [
        self::FULL_TIME => 'fulltime',
        self::PART_TIME => 'parttime',
        self::BOARD_MEMBER => 'board Member'
    ];

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 2;
    const STATUS_LOCKED = 3;

    const STATUS = [
        self::STATUS_ACTIVE => 'Hoạt Động',
        self::STATUS_INACTIVE => 'Không Hoạt Động',
        self::STATUS_LOCKED => 'Đã Khóa'
    ];
}
