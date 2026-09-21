<?php

namespace Tests\Feature\Admin\Authorization;

use App\Const\AdminConst;
use App\Const\GlobalConst;
use App\Models\Admin;
use App\Services\Admin\AdministratorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdministratorProtectionTest extends TestCase
{
    use RefreshDatabase;

    protected function validPayload(Admin $target): array
    {
        return [
            'name' => $target->name,
            'email' => $target->email,
            'role' => $target->role,
            'is_active' => (int) $target->is_active,
        ];
    }

    public function test_admin_cannot_delete_own_account(): void
    {
        $superAdmin = Admin::factory()->superAdmin()->create();
        Admin::factory()->superAdmin()->create();

        $this->actingAs($superAdmin, 'admin')
            ->delete(route('admin.administrators.destroy', $superAdmin->id));

        $this->assertModelExists($superAdmin->fresh());
    }

    public function test_admin_cannot_demote_or_deactivate_own_account(): void
    {
        $superAdmin = Admin::factory()->superAdmin()->create();
        Admin::factory()->superAdmin()->create();

        $payload = $this->validPayload($superAdmin);
        $payload['role'] = AdminConst::ROLE_MANAGER;

        $this->actingAs($superAdmin, 'admin')
            ->patch(route('admin.administrators.update', $superAdmin->id), $payload);

        $this->assertSame(AdminConst::ROLE_SUPER_ADMIN, $superAdmin->fresh()->role);
    }

    public function test_second_active_super_admin_can_be_removed_when_another_remains(): void
    {
        $superAdminA = Admin::factory()->superAdmin()->create();
        $superAdminB = Admin::factory()->superAdmin()->create();

        $this->actingAs($superAdminA, 'admin')
            ->delete(route('admin.administrators.destroy', $superAdminB->id));

        $this->assertSoftDeleted($superAdminB);
    }

    public function test_service_blocks_deleting_the_last_active_super_admin(): void
    {
        $lone = Admin::factory()->superAdmin()->create();
        $otherSuperActor = Admin::factory()->superAdmin()->inactive()->create();

        $result = app(AdministratorService::class)->delete($otherSuperActor, $lone->id);

        $this->assertFalse($result['status']);
        $this->assertModelExists($lone->fresh());
    }

    public function test_service_blocks_demoting_the_last_active_super_admin(): void
    {
        $lone = Admin::factory()->superAdmin()->create();
        $otherSuperActor = Admin::factory()->superAdmin()->inactive()->create();

        $payload = $this->validPayload($lone);
        $payload['role'] = AdminConst::ROLE_MANAGER;

        $result = app(AdministratorService::class)->update($otherSuperActor, $lone->id, $payload);

        $this->assertFalse($result['status']);
        $this->assertSame(AdminConst::ROLE_SUPER_ADMIN, $lone->fresh()->role);
    }

    public function test_service_blocks_deactivating_the_last_active_super_admin(): void
    {
        $lone = Admin::factory()->superAdmin()->create();
        $otherSuperActor = Admin::factory()->superAdmin()->inactive()->create();

        $payload = $this->validPayload($lone);
        $payload['is_active'] = GlobalConst::IS_NOT_ACTIVE;

        $result = app(AdministratorService::class)->update($otherSuperActor, $lone->id, $payload);

        $this->assertFalse($result['status']);
        $this->assertTrue($lone->fresh()->isActive());
    }

    protected function grantAdministratorsManageToManager(): void
    {
        app(\App\Services\Admin\RoleService::class)->update([
            AdminConst::ROLE_MANAGER => array_merge(
                \App\Const\PermissionConst::defaults()[AdminConst::ROLE_MANAGER],
                [\App\Const\PermissionConst::ADMINISTRATORS_MANAGE]
            ),
            AdminConst::ROLE_STAFF => \App\Const\PermissionConst::defaults()[AdminConst::ROLE_STAFF],
        ]);
    }

    public function test_manager_cannot_manage_another_manager_even_when_granted_administrators_manage(): void
    {
        $this->grantAdministratorsManageToManager();

        $manager = Admin::factory()->manager()->create();
        $otherManager = Admin::factory()->manager()->create();

        $this->actingAs($manager, 'admin')
            ->get(route('admin.administrators.edit', $otherManager->id))
            ->assertForbidden();
    }

    public function test_manager_can_manage_staff_once_granted_administrators_manage(): void
    {
        $this->grantAdministratorsManageToManager();

        $manager = Admin::factory()->manager()->create();
        $staff = Admin::factory()->staff()->create();

        $this->actingAs($manager, 'admin')
            ->get(route('admin.administrators.edit', $staff->id))
            ->assertOk();
    }

    public function test_manager_without_administrators_manage_cannot_reach_administrators_pages_at_all(): void
    {
        $manager = Admin::factory()->manager()->create();
        $staff = Admin::factory()->staff()->create();

        $this->actingAs($manager, 'admin')
            ->get(route('admin.administrators.edit', $staff->id))
            ->assertForbidden();
    }

    public function test_updating_role_rotates_remember_token_and_revokes_sessions(): void
    {
        config(['session.driver' => 'database']);

        $superAdmin = Admin::factory()->superAdmin()->create();
        $staff = Admin::factory()->staff()->create();
        $originalToken = $staff->remember_token;

        DB::table('sessions')->insert([
            'id' => 'session-under-test',
            'user_id' => $staff->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'phpunit',
            'payload' => base64_encode('test'),
            'last_activity' => time(),
        ]);

        $payload = $this->validPayload($staff);
        $payload['role'] = AdminConst::ROLE_MANAGER;

        $this->actingAs($superAdmin, 'admin')
            ->patch(route('admin.administrators.update', $staff->id), $payload);

        $this->assertNotSame($originalToken, $staff->fresh()->remember_token);
        $this->assertDatabaseMissing('sessions', ['id' => 'session-under-test']);
    }
}
