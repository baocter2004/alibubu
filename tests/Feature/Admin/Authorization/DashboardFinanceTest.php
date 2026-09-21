<?php

namespace Tests\Feature\Admin\Authorization;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardFinanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_does_not_see_finance_kpis(): void
    {
        $staff = Admin::factory()->staff()->create();

        $response = $this->actingAs($staff, 'admin')->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertDontSee('id="dashboard-revenue-title"', false);
        $response->assertDontSee('id="dashboard-area-title"', false);
        $response->assertDontSee(__('admin/dashboard.period.revenue'));
    }

    public function test_manager_sees_finance_kpis(): void
    {
        $manager = Admin::factory()->manager()->create();

        $response = $this->actingAs($manager, 'admin')->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('id="dashboard-revenue-title"', false);
        $response->assertSee(__('admin/dashboard.period.revenue'));
    }

    public function test_super_admin_sees_finance_kpis(): void
    {
        $superAdmin = Admin::factory()->superAdmin()->create();

        $response = $this->actingAs($superAdmin, 'admin')->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('id="dashboard-revenue-title"', false);
    }
}
