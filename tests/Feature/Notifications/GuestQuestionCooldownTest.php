<?php

namespace Tests\Feature\Notifications;

use App\Services\Client\QuestionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestQuestionCooldownTest extends TestCase
{
    use RefreshDatabase;
    use InteractsWithNotificationFixtures;

    public function test_guest_cooldown_is_scoped_per_guest_and_not_global(): void
    {
        $product = $this->makeProduct();
        $service = app(QuestionService::class);

        request()->server->set('REMOTE_ADDR', '10.0.0.1');
        session()->flush();

        $first = $service->ask($product, null, [
            'question' => 'Does this come with a charger in the box?',
            'fullname' => 'Guest One',
        ]);

        $this->assertTrue($first['status']);

        request()->server->set('REMOTE_ADDR', '10.0.0.2');
        session()->flush();

        $second = $service->ask($product, null, [
            'question' => 'What colours are available for this item?',
            'fullname' => 'Guest Two',
        ]);

        $this->assertTrue(
            $second['status'],
            'A different guest must not be blocked by another guest\'s cooldown on the same product.'
        );

        request()->server->set('REMOTE_ADDR', '10.0.0.1');
        session()->flush();

        $third = $service->ask($product, null, [
            'question' => 'Does this come with a charger in the box, asking again?',
            'fullname' => 'Guest One',
        ]);

        $this->assertFalse(
            $third['status'],
            'The same guest asking again immediately must still be rate-limited.'
        );

        $this->assertSame(2, $product->questions()->count());
    }
}
