<?php

namespace Tests\Feature\Auth;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_returns_the_same_neutral_message_for_unknown_email(): void
    {
        $response = $this->post(route('password.email'), [
            'email' => 'does-not-exist@example.com',
        ]);

        $response->assertRedirect();
        $this->assertNotNull(session('success'));
        $this->assertNull(session('error'));
    }

    public function test_forgot_password_returns_the_same_neutral_message_for_known_email(): void
    {
        $user = User::factory()->create([
            'fullname' => 'Known User',
            'email' => 'known@example.com',
        ]);

        $response = $this->post(route('password.email'), [
            'email' => $user->email,
        ]);

        $response->assertRedirect();
        $this->assertNotNull(session('success'));
        $this->assertNull(session('error'));
    }

    public function test_admin_forgot_password_returns_neutral_message_for_unknown_email(): void
    {
        $response = $this->post(route('admin.password.email'), [
            'email' => 'no-such-admin@example.com',
        ]);

        $response->assertRedirect();
        $this->assertNotNull(session('success'));
        $this->assertNull(session('error'));
    }

    public function test_resetting_password_invalidates_other_sessions(): void
    {
        $user = User::factory()->create([
            'fullname' => 'Reset Me',
            'password' => Hash::make('old-password'),
        ]);

        $originalRememberToken = $user->remember_token;
        $token = Password::broker('users')->createToken($user);

        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'NewPassword123',
            'password_confirmation' => 'NewPassword123',
        ]);

        $response->assertRedirect(route('auth.client.showFormLogin'));

        $user->refresh();
        $this->assertTrue(Hash::check('NewPassword123', $user->password));
        $this->assertNotSame($originalRememberToken, $user->remember_token);
    }

    public function test_admin_resetting_password_rotates_remember_token(): void
    {
        $admin = Admin::factory()->create([
            'password' => Hash::make('old-password'),
        ]);

        $originalRememberToken = $admin->remember_token;
        $token = Password::broker('admins')->createToken($admin);

        $response = $this->post(route('admin.password.update'), [
            'token' => $token,
            'email' => $admin->email,
            'password' => 'NewStrongerPass123',
            'password_confirmation' => 'NewStrongerPass123',
        ]);

        $response->assertRedirect(route('auth.admin.showFormLogin'));

        $admin->refresh();
        $this->assertTrue(Hash::check('NewStrongerPass123', $admin->password));
        $this->assertNotSame($originalRememberToken, $admin->remember_token);
    }
}
