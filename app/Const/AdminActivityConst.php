<?php

namespace App\Const;

class AdminActivityConst
{
    const PER_PAGE = 20;

    const ROLES_PERMISSIONS_UPDATED = 'roles.permissions_updated';
    const ROLES_PERMISSIONS_RESET = 'roles.permissions_reset';
    const ADMINISTRATORS_CREATED = 'administrators.created';
    const ADMINISTRATORS_UPDATED = 'administrators.updated';
    const ADMINISTRATORS_DELETED = 'administrators.deleted';

    public static function actionLabel(string $action): string
    {
        $key = 'admin/role.activity.actions.' . $action;
        $label = __($key);

        return $label === $key ? $action : $label;
    }
}
