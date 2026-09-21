<?php

namespace Tests\Feature\Notifications;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminNotificationOwnershipTest extends TestCase
{
    use RefreshDatabase;
    use InteractsWithNotificationFixtures;

    public function test_an_admin_cannot_mark_another_admins_notification_as_read(): void
    {
        $owner = $this->makeAdmin();
        $intruder = $this->makeAdmin();

        $owner->notify(new PingNotification());
        $ownerNotification = $owner->fresh()->notifications()->firstOrFail();

        $this->actingAs($intruder, 'admin')
            ->post(route('admin.notifications.read', $ownerNotification->id))
            ->assertRedirect();

        $this->assertNull($ownerNotification->fresh()->read_at);
    }

    public function test_an_admin_only_sees_their_own_notifications_in_the_index(): void
    {
        $owner = $this->makeAdmin();
        $other = $this->makeAdmin();

        $owner->notify(new PingNotification());
        $other->notify(new PingNotification());

        $this->actingAs($owner, 'admin')
            ->get(route('admin.notifications.index'))
            ->assertOk();

        $this->assertSame(1, $owner->fresh()->notifications()->count());
        $this->assertSame(1, $other->fresh()->notifications()->count());
    }
}
