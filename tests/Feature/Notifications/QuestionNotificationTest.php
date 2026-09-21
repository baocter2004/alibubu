<?php

namespace Tests\Feature\Notifications;

use App\Const\AdminConst;
use App\Const\PermissionConst;
use App\Models\ProductQuestion;
use App\Notifications\QuestionAnswered;
use App\Services\Admin\QuestionService as AdminQuestionService;
use App\Services\Client\QuestionService as ClientQuestionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class QuestionNotificationTest extends TestCase
{
    use RefreshDatabase;
    use InteractsWithNotificationFixtures;

    public function test_asking_a_question_notifies_admins_with_a_working_url(): void
    {
        $this->grantRolePermissions([
            AdminConst::ROLE_MANAGER => [PermissionConst::QUESTIONS_ANSWER],
            AdminConst::ROLE_STAFF => [],
        ]);

        $admin = $this->makeAdmin(AdminConst::ROLE_MANAGER);
        $product = $this->makeProduct();

        $result = app(ClientQuestionService::class)->ask($product, null, [
            'question' => 'Does this product come with a warranty card?',
            'fullname' => 'Guest Asker',
            'email' => 'guest@example.com',
        ]);

        $this->assertTrue($result['status']);

        $notification = $admin->fresh()->notifications()->firstOrFail();

        $this->assertSame('question.asked', $notification->data['type']);
        $this->assertStringContainsString('/admin/questions', $notification->data['url']);
        $this->assertStringContainsString('#question-', $notification->data['url']);
    }

    public function test_answering_notifies_the_logged_in_asker(): void
    {
        $customer = $this->makeCustomer();
        $product = $this->makeProduct();
        $admin = $this->makeAdmin();

        $question = ProductQuestion::create([
            'product_id' => $product->id,
            'user_id' => $customer->id,
            'question' => 'Is this in stock right now?',
            'is_published' => false,
        ]);

        app(AdminQuestionService::class)->answer($question->id, 'Yes, it is in stock.', $admin);

        $notification = $customer->fresh()->notifications()->firstOrFail();

        $this->assertSame('question.answered', $notification->data['type']);
        $this->assertSame($question->id, $notification->data['question_id']);
    }

    public function test_answering_a_guest_question_notifies_the_captured_email(): void
    {
        Notification::fake();

        $product = $this->makeProduct();
        $admin = $this->makeAdmin();

        $question = ProductQuestion::create([
            'product_id' => $product->id,
            'user_id' => null,
            'fullname' => 'Guest Asker',
            'email' => 'guest-asker@example.com',
            'question' => 'Do you ship internationally?',
            'is_published' => false,
        ]);

        app(AdminQuestionService::class)->answer($question->id, 'Yes we do.', $admin);

        Notification::assertSentOnDemand(
            QuestionAnswered::class,
            fn ($notification, $channels, $notifiable) => ($notifiable->routes['mail'] ?? null) === 'guest-asker@example.com'
        );
    }
}
