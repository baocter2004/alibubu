<?php

namespace Tests\Feature\Auth;

use App\Mail\VerifyUserEmail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_unverified_user_can_resend_verification_email(): void
    {
        Mail::fake();

        $user = User::factory()->unverified()->create(['fullname' => 'Needs Verify']);

        $response = $this->actingAs($user, 'user')
            ->post(route('verification.send'));

        $response->assertRedirect();
        $this->assertNotNull(session('success'));

        Mail::assertQueued(VerifyUserEmail::class, fn ($mail) => $mail->user->is($user));
    }

    public function test_already_verified_user_does_not_get_another_email(): void
    {
        Mail::fake();

        $user = User::factory()->create(['fullname' => 'Already Verified']);

        $this->actingAs($user, 'user')->post(route('verification.send'));

        Mail::assertNothingQueued();
    }

    public function test_guest_cannot_resend_verification_email(): void
    {
        $response = $this->post(route('verification.send'));

        $response->assertRedirect(route('auth.client.showFormLogin'));
    }
}
