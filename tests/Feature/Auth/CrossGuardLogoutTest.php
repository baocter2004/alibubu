<?php

namespace Tests\Feature\Auth;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrossGuardLogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_logout_does_not_log_out_admin(): void
    {
        $user = User::factory()->create(['fullname' => 'Customer']);
        $admin = Admin::factory()->create();

        $this->actingAs($user, 'user');
        $this->actingAs($admin, 'admin');

        $this->post(route('auth.client.logout'))->assertRedirect(route('index'));

        $this->assertGuest('user');
        $this->assertAuthenticatedAs($admin, 'admin');
    }

    public function test_admin_logout_does_not_log_out_client(): void
    {
        $user = User::factory()->create(['fullname' => 'Customer']);
        $admin = Admin::factory()->create();

        $this->actingAs($user, 'user');
        $this->actingAs($admin, 'admin');

        $this->post(route('auth.admin.logout'))->assertRedirect(route('auth.admin.showFormLogin'));

        $this->assertGuest('admin');
        $this->assertAuthenticatedAs($user, 'user');
    }
}
