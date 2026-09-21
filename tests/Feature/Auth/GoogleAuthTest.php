<?php

namespace Tests\Feature\Auth;

use App\Const\UserConst;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Tests\TestCase;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_verified_google_user_is_created_and_logged_in(): void
    {
        Socialite::fake('google', SocialiteUser::fake([
            'id' => 'google-new-1',
            'email' => 'brandnew@example.com',
            'name' => 'Brand New',
            'email_verified' => true,
        ]));

        $response = $this->get(route('auth.client.handleGoogleCallback'));

        $response->assertRedirect(route('index'));
        $this->assertAuthenticated('user');

        $user = User::where('email', 'brandnew@example.com')->firstOrFail();
        $this->assertSame('google-new-1', $user->google_id);
        $this->assertNotNull($user->email_verified_at);
    }

    public function test_google_signup_is_rejected_when_google_email_is_unverified(): void
    {
        Socialite::fake('google', SocialiteUser::fake([
            'id' => 'google-unverified-1',
            'email' => 'unverified@example.com',
            'email_verified' => false,
        ]));

        $response = $this->get(route('auth.client.handleGoogleCallback'));

        $response->assertRedirect(route('auth.client.showFormLogin'));
        $this->assertGuest('user');
        $this->assertDatabaseMissing('users', ['email' => 'unverified@example.com']);
    }

    public function test_google_auto_links_when_both_emails_are_verified(): void
    {
        $user = User::factory()->create([
            'fullname' => 'Existing Verified',
            'email' => 'verified@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        Socialite::fake('google', SocialiteUser::fake([
            'id' => 'google-link-1',
            'email' => 'verified@example.com',
            'email_verified' => true,
        ]));

        $response = $this->get(route('auth.client.handleGoogleCallback'));

        $response->assertRedirect(route('index'));
        $this->assertAuthenticatedAs($user->fresh(), 'user');
        $this->assertSame('google-link-1', $user->fresh()->google_id);
    }

    public function test_google_requires_password_login_first_when_local_email_unverified(): void
    {
        $user = User::factory()->create([
            'fullname' => 'Unverified Local',
            'email' => 'localunverified@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => null,
        ]);

        Socialite::fake('google', SocialiteUser::fake([
            'id' => 'google-pending-1',
            'email' => 'localunverified@example.com',
            'email_verified' => true,
        ]));

        $response = $this->get(route('auth.client.handleGoogleCallback'));

        $response->assertRedirect(route('auth.client.showFormLogin'));
        $this->assertGuest('user');
        $this->assertNull($user->fresh()->google_id);
    }

    public function test_google_conflicts_when_account_already_linked_to_another_google_id(): void
    {
        User::factory()->create([
            'fullname' => 'Already Linked',
            'email' => 'linked@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'google_id' => 'existing-google-id',
        ]);

        Socialite::fake('google', SocialiteUser::fake([
            'id' => 'a-different-google-id',
            'email' => 'linked@example.com',
            'email_verified' => true,
        ]));

        $response = $this->get(route('auth.client.handleGoogleCallback'));

        $response->assertRedirect(route('auth.client.showFormLogin'));
        $this->assertGuest('user');
    }

    public function test_inactive_user_cannot_login_via_google(): void
    {
        User::factory()->create([
            'fullname' => 'Locked Google',
            'email' => 'lockedgoogle@example.com',
            'password' => Hash::make('password'),
            'google_id' => 'locked-google-id',
            'status' => UserConst::STATUS_LOCKED,
        ]);

        Socialite::fake('google', SocialiteUser::fake([
            'id' => 'locked-google-id',
            'email' => 'lockedgoogle@example.com',
            'email_verified' => true,
        ]));

        $response = $this->get(route('auth.client.handleGoogleCallback'));

        $response->assertRedirect(route('auth.client.showFormLogin'));
        $this->assertGuest('user');
    }
}
