<?php

namespace Tests\Feature\Auth;

use App\Const\UserConst;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserActiveStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_locked_user_cannot_login_with_password(): void
    {
        $user = User::factory()->create([
            'fullname' => 'Locked User',
            'password' => Hash::make('password'),
            'status' => UserConst::STATUS_LOCKED,
            'reason_lock' => 'Vi phạm điều khoản',
        ]);

        $response = $this->post(route('auth.client.handleLogin'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect();
        $this->assertGuest('user');
        $this->assertStringContainsString('Vi phạm điều khoản', session('error'));
    }

    public function test_inactive_user_cannot_login_with_password(): void
    {
        $user = User::factory()->create([
            'fullname' => 'Inactive User',
            'password' => Hash::make('password'),
            'status' => UserConst::STATUS_INACTIVE,
        ]);

        $response = $this->post(route('auth.client.handleLogin'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect();
        $this->assertGuest('user');
    }

    public function test_locking_user_mid_session_logs_them_out_on_next_request(): void
    {
        $user = User::factory()->create([
            'fullname' => 'Session User',
            'password' => Hash::make('password'),
            'status' => UserConst::STATUS_ACTIVE,
        ]);

        $this->actingAs($user, 'user');

        $user->forceFill(['status' => UserConst::STATUS_LOCKED])->save();

        $response = $this->get(route('index'));

        $response->assertRedirect(route('auth.client.showFormLogin'));
        $this->assertGuest('user');
    }

    public function test_active_user_can_login(): void
    {
        $user = User::factory()->create([
            'fullname' => 'Active User',
            'password' => Hash::make('password'),
            'status' => UserConst::STATUS_ACTIVE,
        ]);

        $response = $this->post(route('auth.client.handleLogin'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('index'));
        $this->assertAuthenticatedAs($user, 'user');
    }
}
