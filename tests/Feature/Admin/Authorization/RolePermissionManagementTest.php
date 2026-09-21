<?php

namespace Tests\Feature\Admin\Authorization;

use App\Const\AdminActivityConst;
use App\Const\AdminConst;
use App\Const\PermissionConst;
use App\Models\Admin;
use App\Models\AdminActivityLog;
use App\Models\AdminRolePermission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class RolePermissionManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_super_admin_cannot_open_permission_page(): void
    {
        $manager = Admin::factory()->manager()->create();

        $this->actingAs($manager, 'admin')
            ->get(route('admin.roles.index'))
            ->assertForbidden();
    }

    public function test_super_admin_can_update_permissions_and_it_takes_effect_immediately(): void
    {
        $superAdmin = Admin::factory()->superAdmin()->create();
        $staff = Admin::factory()->staff()->create();

        $this->assertTrue(Gate::forUser($staff)->allows(PermissionConst::PRODUCTS_CREATE));

        $payload = PermissionConst::defaults();
        $payload[AdminConst::ROLE_STAFF] = array_values(array_diff(
            $payload[AdminConst::ROLE_STAFF],
            [PermissionConst::PRODUCTS_CREATE]
        ));

        $this->actingAs($superAdmin, 'admin')
            ->put(route('admin.roles.update'), ['permissions' => $payload])
            ->assertRedirect(route('admin.roles.index'));

        $this->assertDatabaseMissing('admin_role_permissions', [
            'role' => AdminConst::ROLE_STAFF,
            'permission' => PermissionConst::PRODUCTS_CREATE,
        ]);

        $freshStaff = $staff->fresh();
        $this->assertFalse(Gate::forUser($freshStaff)->allows(PermissionConst::PRODUCTS_CREATE));

        $this->assertDatabaseHas('admin_activity_logs', [
            'admin_id' => $superAdmin->id,
            'action' => AdminActivityConst::ROLES_PERMISSIONS_UPDATED,
        ]);
    }

    public function test_update_rejects_super_admin_only_permission_and_non_editable_role_key(): void
    {
        $superAdmin = Admin::factory()->superAdmin()->create();

        $payload = [
            AdminConst::ROLE_MANAGER => [PermissionConst::ROLES_MANAGE, PermissionConst::PRODUCTS_VIEW],
            AdminConst::ROLE_SUPER_ADMIN => [PermissionConst::PRODUCTS_VIEW],
        ];

        $this->actingAs($superAdmin, 'admin')
            ->from(route('admin.roles.index'))
            ->put(route('admin.roles.update'), ['permissions' => $payload])
            ->assertSessionHasErrors();

        $this->assertDatabaseMissing('admin_role_permissions', [
            'role' => AdminConst::ROLE_MANAGER,
            'permission' => PermissionConst::ROLES_MANAGE,
        ]);
    }

    public function test_service_never_persists_super_admin_only_ability_for_editable_roles(): void
    {
        $roleService = app(\App\Services\Admin\RoleService::class);

        $roleService->update([
            AdminConst::ROLE_MANAGER => [PermissionConst::ROLES_MANAGE, PermissionConst::PRODUCTS_VIEW],
            AdminConst::ROLE_STAFF => [PermissionConst::PRODUCTS_VIEW],
        ]);

        $this->assertDatabaseMissing('admin_role_permissions', [
            'role' => AdminConst::ROLE_MANAGER,
            'permission' => PermissionConst::ROLES_MANAGE,
        ]);

        $this->assertSame(
            [PermissionConst::SUPER_ADMIN_MARKER],
            AdminRolePermission::where('role', AdminConst::ROLE_SUPER_ADMIN)->pluck('permission')->all()
        );
    }

    public function test_reset_restores_defaults_and_logs_activity(): void
    {
        $superAdmin = Admin::factory()->superAdmin()->create();

        AdminRolePermission::insert([
            'role' => AdminConst::ROLE_STAFF,
            'permission' => PermissionConst::ADMINISTRATORS_MANAGE,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($superAdmin, 'admin')
            ->post(route('admin.roles.reset'))
            ->assertRedirect(route('admin.roles.index'));

        $this->assertDatabaseMissing('admin_role_permissions', [
            'role' => AdminConst::ROLE_STAFF,
            'permission' => PermissionConst::ADMINISTRATORS_MANAGE,
        ]);

        foreach (PermissionConst::defaults()[AdminConst::ROLE_STAFF] as $permission) {
            $this->assertDatabaseHas('admin_role_permissions', [
                'role' => AdminConst::ROLE_STAFF,
                'permission' => $permission,
            ]);
        }

        $this->assertDatabaseHas('admin_activity_logs', [
            'action' => AdminActivityConst::ROLES_PERMISSIONS_RESET,
        ]);
    }

    public function test_activity_log_is_only_visible_to_roles_manage(): void
    {
        $superAdmin = Admin::factory()->superAdmin()->create();
        $manager = Admin::factory()->manager()->create();

        AdminActivityLog::query()->create([
            'admin_id' => $superAdmin->id,
            'action' => AdminActivityConst::ROLES_PERMISSIONS_RESET,
        ]);

        $this->actingAs($superAdmin, 'admin')
            ->get(route('admin.roles.activity'))
            ->assertOk();

        $this->actingAs($manager, 'admin')
            ->get(route('admin.roles.activity'))
            ->assertForbidden();
    }
}
