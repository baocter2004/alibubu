<?php

namespace Tests\Feature\Admin\Authorization;

use App\Const\AdminConst;
use App\Const\PermissionConst;
use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RouteAccessTest extends TestCase
{
    use RefreshDatabase;

    public static function routeAbilityProvider(): array
    {
        return [
            'dashboard' => ['admin.dashboard', PermissionConst::DASHBOARD_VIEW],
            'administrators.index' => ['admin.administrators.index', PermissionConst::ADMINISTRATORS_MANAGE],
            'roles.index' => ['admin.roles.index', PermissionConst::ROLES_MANAGE],
            'products.index' => ['admin.products.index', PermissionConst::PRODUCTS_VIEW],
            'products.create' => ['admin.products.create', PermissionConst::PRODUCTS_CREATE],
            'products.import' => ['admin.products.import', PermissionConst::PRODUCTS_IMPORT],
            'categories.index' => ['admin.categories.index', PermissionConst::CATEGORIES_VIEW],
            'categories.create' => ['admin.categories.create', PermissionConst::CATEGORIES_MANAGE],
            'tags.index' => ['admin.tags.index', PermissionConst::TAGS_VIEW],
            'tags.create' => ['admin.tags.create', PermissionConst::TAGS_MANAGE],
            'attributes.index' => ['admin.attributes.index', PermissionConst::ATTRIBUTES_VIEW],
            'attributes.create' => ['admin.attributes.create', PermissionConst::ATTRIBUTES_MANAGE],
            'branches.index' => ['admin.branches.index', PermissionConst::BRANCHES_VIEW],
            'branches.create' => ['admin.branches.create', PermissionConst::BRANCHES_MANAGE],
            'coupons.index' => ['admin.coupons.index', PermissionConst::COUPONS_VIEW],
            'coupons.create' => ['admin.coupons.create', PermissionConst::COUPONS_MANAGE],
            'users.index' => ['admin.users.index', PermissionConst::USERS_VIEW],
            'users.create' => ['admin.users.create', PermissionConst::USERS_MANAGE],
            'reviews.index' => ['admin.reviews.index', PermissionConst::REVIEWS_VIEW],
            'questions.index' => ['admin.questions.index', PermissionConst::QUESTIONS_VIEW],
            'orders.index' => ['admin.orders.index', PermissionConst::ORDERS_VIEW],
            'provinces.index' => ['admin.provinces.index', PermissionConst::LOCATIONS_VIEW],
        ];
    }

    public static function roleProvider(): array
    {
        return [
            'super_admin' => [AdminConst::ROLE_SUPER_ADMIN],
            'manager' => [AdminConst::ROLE_MANAGER],
            'staff' => [AdminConst::ROLE_STAFF],
        ];
    }

    /**
     * @dataProvider routeAbilityProvider
     */
    public function test_route_allows_or_denies_per_role(string $routeName, string $ability): void
    {
        foreach (self::roleProvider() as [$role]) {
            $admin = Admin::factory()->create(['role' => $role]);
            $allowed = $role === AdminConst::ROLE_SUPER_ADMIN
                || in_array($ability, \App\Const\PermissionConst::defaultsFor($role), true);

            $response = $this->actingAs($admin, 'admin')->get(route($routeName));

            if ($allowed) {
                $response->assertOk();
            } else {
                $response->assertForbidden();
            }
        }
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('auth.admin.showFormLogin'));
    }

    public function test_inactive_admin_is_logged_out_and_redirected(): void
    {
        $admin = Admin::factory()->superAdmin()->inactive()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.dashboard'));

        $response->assertRedirect(route('auth.admin.showFormLogin'));
        $this->assertGuest('admin');
    }
}
