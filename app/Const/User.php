<?

namespace App\Const;

class User
{
    const ROLE_USER = 1;
    const ROLE_EMPLOYEE = 2;
    const ROLE_ADMIN = 3;


    const ROLE = [
        self::ROLE_USER => 'user',
        self::ROLE_EMPLOYEE => 'employee',
        self::ROLE_ADMIN => 'admin'
    ];

    const MALE = 1;
    const FEMALE = 2;
    const OTHER = 3;

    const GENDER = [
        1 => 'male',
        2 => 'female',
        3 => 'other'
    ];

    const NOT_SELECTED = 0;
    const YES = 1;
    const NOT = 2;

    const YES_NO_OPTIONS = [
        self::NOT_SELECTED => 'not selected',
        self::YES => 'yes',
        self::NOT => 'no',
    ];

    const YES_NO_ONLY = [
        self::YES => 'yes',
        self::NOT => 'no',
    ];

    const FULL_TIME = 1;
    const PART_TIME = 2;
    const BOARD_MEMBER = 3;

    const EMPLOYEE_TYPES = [
        self::FULL_TIME => 'fulltime',
        self::PART_TIME => 'parttime',
        self::BOARD_MEMBER => 'board Member'
    ];
}
