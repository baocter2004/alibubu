<?php

namespace Tests\Feature\Admin\Authorization;

use App\Const\AdminConst;
use App\Const\PermissionConst;
use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class PermissionGateTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_passes_every_ability(): void
    {
        $superAdmin = Admin::factory()->superAdmin()->create();

        foreach (PermissionConst::all() as $ability) {
            $this->assertTrue(Gate::forUser($superAdmin)->allows($ability), $ability);
        }
    }

    public function test_manager_matches_default_matrix(): void
    {
        $manager = Admin::factory()->manager()->create();
        $expected = PermissionConst::defaults()[AdminConst::ROLE_MANAGER];

        foreach (PermissionConst::all() as $ability) {
            $this->assertSame(
                in_array($ability, $expected, true),
                Gate::forUser($manager)->allows($ability),
                $ability
            );
        }
    }

    public function test_staff_matches_default_matrix(): void
    {
        $staff = Admin::factory()->staff()->create();
        $expected = PermissionConst::defaults()[AdminConst::ROLE_STAFF];

        foreach (PermissionConst::all() as $ability) {
            $this->assertSame(
                in_array($ability, $expected, true),
                Gate::forUser($staff)->allows($ability),
                $ability
            );
        }
    }

    public function test_inactive_admin_has_no_abilities(): void
    {
        $inactive = Admin::factory()->superAdmin()->inactive()->create();

        foreach (PermissionConst::all() as $ability) {
            $this->assertFalse(Gate::forUser($inactive)->allows($ability), $ability);
        }
    }

    public function test_manager_never_has_force_delete_or_system_abilities(): void
    {
        $manager = Admin::factory()->manager()->create();

        $this->assertFalse(Gate::forUser($manager)->allows(PermissionConst::ADMINISTRATORS_MANAGE));
        $this->assertFalse(Gate::forUser($manager)->allows(PermissionConst::ROLES_MANAGE));

        foreach (PermissionConst::all() as $ability) {
            if (str_ends_with($ability, '.force_delete')) {
                $this->assertFalse(Gate::forUser($manager)->allows($ability), $ability);
            }
        }
    }
}
