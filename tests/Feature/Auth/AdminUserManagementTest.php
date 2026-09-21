<?php

namespace Tests\Feature\Auth;

use App\Const\AdminConst;
use App\Const\UserConst;
use App\Models\Admin;
use App\Models\Province;
use App\Models\User;
use App\Models\Ward;
use App\Notifications\UserResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function superAdmin(): Admin
    {
        return Admin::factory()->create(['role' => AdminConst::ROLE_SUPER_ADMIN]);
    }

    protected function makeProvinceAndWard(): array
    {
        $province = Province::create([
            'name' => 'Ha Noi',
            'code' => 'HN01',
            'division_type' => 'thanh-pho',
            'codename' => 'ha_noi',
            'phone_code' => 24,
        ]);
        $ward = Ward::create([
            'name' => 'Ba Dinh',
            'code' => 'BD01',
            'division_type' => 'phuong',
            'codename' => 'ba_dinh',
            'province_id' => $province->id,
        ]);

        return [$province, $ward];
    }

    public function test_admin_can_create_a_customer_without_submitting_a_password(): void
    {
        [$province, $ward] = $this->makeProvinceAndWard();
        $admin = $this->superAdmin();

        $payload = [
            'fullname' => 'New Customer',
            'email' => 'newcustomer@example.com',
            'phone_number' => '0900000001',
            'status' => UserConst::STATUS_ACTIVE,
            'user_addresses' => [
                [
                    'fullname' => 'New Customer',
                    'phone_number' => '0900000001',
                    'province_id' => $province->id,
                    'ward_id' => $ward->id,
                    'address' => '123 Test Street',
                    'is_default' => true,
                ],
            ],
        ];

        $this->assertArrayNotHasKey('password', $payload);

        $confirm = $this->actingAs($admin, 'admin')
            ->post(route('admin.users.confirm'), $payload);

        $confirm->assertRedirect(route('admin.users.confirm-detail'));

        $save = $this->post(route('admin.users.save'));
        $save->assertRedirect(route('admin.users.index'));

        $user = User::where('email', 'newcustomer@example.com')->firstOrFail();
        $this->assertSame(UserConst::STATUS_ACTIVE, (int) $user->status);
        $this->assertNotEmpty($user->password);
    }

    public function test_post_user_request_rejects_a_submitted_password_field_silently(): void
    {
        $rules = (new \App\Http\Requests\Admin\Users\PostUserRequest())->rules();

        $this->assertArrayNotHasKey('password', $rules);
        $this->assertArrayNotHasKey('role', $rules);
    }

    public function test_admin_can_send_a_password_reset_link_instead_of_setting_one(): void
    {
        Notification::fake();

        $admin = $this->superAdmin();
        $user = User::factory()->create(['fullname' => 'Reset Target']);

        $response = $this->actingAs($admin, 'admin')
            ->post(route('admin.users.send-reset-link', $user->id));

        $response->assertRedirect();
        $this->assertNotNull(session('success'));

        Notification::assertSentTo($user, UserResetPassword::class);
    }
}
