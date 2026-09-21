<?php

namespace Tests\Feature\Notifications;

use App\Const\AdminConst;
use App\Const\PermissionConst;
use App\Services\Admin\AdminNotifierService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminNotifierServiceTest extends TestCase
{
    use RefreshDatabase;
    use InteractsWithNotificationFixtures;

    public function test_it_notifies_only_active_admins_with_the_ability(): void
    {
        $this->grantRolePermissions([
            AdminConst::ROLE_MANAGER => [PermissionConst::REVIEWS_MODERATE],
            AdminConst::ROLE_STAFF => [],
        ]);

        $withAbility = $this->makeAdmin(AdminConst::ROLE_MANAGER);
        $withoutAbility = $this->makeAdmin(AdminConst::ROLE_STAFF);
        $inactiveWithAbility = $this->makeAdmin(AdminConst::ROLE_MANAGER, ['is_active' => false]);
        $superAdmin = $this->makeAdmin(AdminConst::ROLE_SUPER_ADMIN);

        AdminNotifierService::notify(new PingNotification(), PermissionConst::REVIEWS_MODERATE);

        $this->assertSame(1, $withAbility->fresh()->notifications()->count());
        $this->assertSame(0, $withoutAbility->fresh()->notifications()->count());
        $this->assertSame(0, $inactiveWithAbility->fresh()->notifications()->count());
        $this->assertSame(1, $superAdmin->fresh()->notifications()->count());
    }

    public function test_recipients_treats_missing_active_flag_as_active(): void
    {
        $this->grantRolePermissions([
            AdminConst::ROLE_MANAGER => [PermissionConst::QUESTIONS_ANSWER],
            AdminConst::ROLE_STAFF => [],
        ]);

        $admin = $this->makeAdmin(AdminConst::ROLE_MANAGER);
        $admin->forceFill(['is_active' => null])->saveQuietly();

        $recipients = AdminNotifierService::recipients(PermissionConst::QUESTIONS_ANSWER);

        $this->assertTrue($recipients->contains('id', $admin->id));
    }
}
