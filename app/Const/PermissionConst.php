<?php

namespace App\Const;

class PermissionConst
{
    const DASHBOARD_VIEW = 'dashboard.view';
    const DASHBOARD_FINANCE = 'dashboard.finance';

    const ORDERS_VIEW = 'orders.view';
    const ORDERS_UPDATE_STATUS = 'orders.update_status';
    const ORDERS_MARK_PAID = 'orders.mark_paid';
    const ORDERS_REFUND = 'orders.refund';

    const PRODUCTS_VIEW = 'products.view';
    const PRODUCTS_CREATE = 'products.create';
    const PRODUCTS_UPDATE = 'products.update';
    const PRODUCTS_DELETE = 'products.delete';
    const PRODUCTS_FORCE_DELETE = 'products.force_delete';
    const PRODUCTS_IMPORT = 'products.import';

    const CATEGORIES_VIEW = 'categories.view';
    const CATEGORIES_MANAGE = 'categories.manage';
    const CATEGORIES_FORCE_DELETE = 'categories.force_delete';

    const TAGS_VIEW = 'tags.view';
    const TAGS_MANAGE = 'tags.manage';
    const TAGS_FORCE_DELETE = 'tags.force_delete';

    const ATTRIBUTES_VIEW = 'attributes.view';
    const ATTRIBUTES_MANAGE = 'attributes.manage';
    const ATTRIBUTES_FORCE_DELETE = 'attributes.force_delete';

    const BRANCHES_VIEW = 'branches.view';
    const BRANCHES_MANAGE = 'branches.manage';
    const BRANCHES_FORCE_DELETE = 'branches.force_delete';

    const COUPONS_VIEW = 'coupons.view';
    const COUPONS_MANAGE = 'coupons.manage';
    const COUPONS_FORCE_DELETE = 'coupons.force_delete';

    const USERS_VIEW = 'users.view';
    const USERS_MANAGE = 'users.manage';
    const USERS_FORCE_DELETE = 'users.force_delete';

    const REVIEWS_VIEW = 'reviews.view';
    const REVIEWS_MODERATE = 'reviews.moderate';
    const REVIEWS_DELETE = 'reviews.delete';

    const QUESTIONS_VIEW = 'questions.view';
    const QUESTIONS_ANSWER = 'questions.answer';
    const QUESTIONS_DELETE = 'questions.delete';

    const LOCATIONS_VIEW = 'locations.view';

    const ADMINISTRATORS_MANAGE = 'administrators.manage';
    const ROLES_MANAGE = 'roles.manage';

    const SUPER_ADMIN_MARKER = '*';

    public static function groups(): array
    {
        return [
            'dashboard' => [self::DASHBOARD_VIEW, self::DASHBOARD_FINANCE],
            'orders' => [self::ORDERS_VIEW, self::ORDERS_UPDATE_STATUS, self::ORDERS_MARK_PAID, self::ORDERS_REFUND],
            'products' => [
                self::PRODUCTS_VIEW,
                self::PRODUCTS_CREATE,
                self::PRODUCTS_UPDATE,
                self::PRODUCTS_DELETE,
                self::PRODUCTS_FORCE_DELETE,
                self::PRODUCTS_IMPORT,
            ],
            'catalog' => [
                self::CATEGORIES_VIEW,
                self::CATEGORIES_MANAGE,
                self::CATEGORIES_FORCE_DELETE,
                self::TAGS_VIEW,
                self::TAGS_MANAGE,
                self::TAGS_FORCE_DELETE,
                self::ATTRIBUTES_VIEW,
                self::ATTRIBUTES_MANAGE,
                self::ATTRIBUTES_FORCE_DELETE,
                self::BRANCHES_VIEW,
                self::BRANCHES_MANAGE,
                self::BRANCHES_FORCE_DELETE,
            ],
            'coupons' => [self::COUPONS_VIEW, self::COUPONS_MANAGE, self::COUPONS_FORCE_DELETE],
            'customers' => [self::USERS_VIEW, self::USERS_MANAGE, self::USERS_FORCE_DELETE],
            'reviews' => [self::REVIEWS_VIEW, self::REVIEWS_MODERATE, self::REVIEWS_DELETE],
            'questions' => [self::QUESTIONS_VIEW, self::QUESTIONS_ANSWER, self::QUESTIONS_DELETE],
            'locations' => [self::LOCATIONS_VIEW],
            'system' => [self::ADMINISTRATORS_MANAGE, self::ROLES_MANAGE],
        ];
    }

    public static function all(): array
    {
        return array_merge(...array_values(self::groups()));
    }

    public static function isValid(string $permission): bool
    {
        return in_array($permission, self::all(), true);
    }

    public static function label(string $permission): string
    {
        return __('admin/role.permissions.' . str_replace('.', '_', $permission));
    }

    public static function groupLabel(string $group): string
    {
        return __('admin/role.groups.' . $group);
    }

    public static function superAdminOnly(): array
    {
        return [self::ROLES_MANAGE];
    }

    public static function defaults(): array
    {
        $all = self::all();

        return [
            AdminConst::ROLE_MANAGER => array_values(array_filter(
                $all,
                fn (string $permission) => ! in_array($permission, [self::ADMINISTRATORS_MANAGE, self::ROLES_MANAGE], true)
                    && ! str_ends_with($permission, '.force_delete')
            )),
            AdminConst::ROLE_STAFF => [
                self::DASHBOARD_VIEW,
                self::ORDERS_VIEW,
                self::ORDERS_UPDATE_STATUS,
                self::PRODUCTS_VIEW,
                self::PRODUCTS_CREATE,
                self::PRODUCTS_UPDATE,
                self::CATEGORIES_VIEW,
                self::TAGS_VIEW,
                self::ATTRIBUTES_VIEW,
                self::BRANCHES_VIEW,
                self::COUPONS_VIEW,
                self::USERS_VIEW,
                self::REVIEWS_VIEW,
                self::REVIEWS_MODERATE,
                self::QUESTIONS_VIEW,
                self::QUESTIONS_ANSWER,
                self::LOCATIONS_VIEW,
            ],
        ];
    }

    public static function defaultsFor(int $role): array
    {
        if ($role === AdminConst::ROLE_SUPER_ADMIN) {
            return self::all();
        }

        return self::defaults()[$role] ?? [];
    }
}
