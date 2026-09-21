<?php

namespace Tests\Feature\Notifications;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientNotificationCenterTest extends TestCase
{
    use RefreshDatabase;
    use InteractsWithNotificationFixtures;

    public function test_customer_can_list_and_mark_a_notification_as_read(): void
    {
        $customer = $this->makeCustomer();
        $customer->notify(new PingNotification());

        $notification = $customer->fresh()->notifications()->firstOrFail();

        $this->actingAs($customer, 'user')
            ->get(route('account.notifications.index'))
            ->assertOk();

        $this->actingAs($customer, 'user')
            ->post(route('account.notifications.read', $notification->id))
            ->assertRedirect();

        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_customer_can_mark_all_notifications_as_read(): void
    {
        $customer = $this->makeCustomer();
        $customer->notify(new PingNotification());
        $customer->notify(new PingNotification());

        $this->actingAs($customer, 'user')
            ->post(route('account.notifications.read-all'))
            ->assertRedirect();

        $this->assertSame(0, $customer->fresh()->unreadNotifications()->count());
    }

    public function test_customer_only_sees_their_own_notifications(): void
    {
        $customer = $this->makeCustomer();
        $other = $this->makeCustomer(['email' => 'other@example.com']);

        $other->notify(new PingNotification());

        $this->actingAs($customer, 'user')
            ->post(route('account.notifications.read', $other->fresh()->notifications()->firstOrFail()->id))
            ->assertRedirect();

        $this->assertNull($other->fresh()->notifications()->firstOrFail()->read_at);
    }
}
