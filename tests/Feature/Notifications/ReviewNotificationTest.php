<?php

namespace Tests\Feature\Notifications;

use App\Const\AdminConst;
use App\Const\PermissionConst;
use App\Services\Admin\ReviewService as AdminReviewService;
use App\Services\Client\ReviewService as ClientReviewService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewNotificationTest extends TestCase
{
    use RefreshDatabase;
    use InteractsWithNotificationFixtures;

    public function test_submitting_a_review_notifies_admins_with_the_ability(): void
    {
        $this->grantRolePermissions([
            AdminConst::ROLE_MANAGER => [PermissionConst::REVIEWS_MODERATE],
            AdminConst::ROLE_STAFF => [],
        ]);

        $admin = $this->makeAdmin(AdminConst::ROLE_MANAGER);
        $other = $this->makeAdmin(AdminConst::ROLE_STAFF);

        $product = $this->makeProduct();
        $customer = $this->makeCustomer();
        $this->makeCompletedOrder($customer, $product);

        $result = app(ClientReviewService::class)->store($product, $customer, [
            'rating' => 5,
            'title' => 'Great product',
            'comment' => 'Works perfectly.',
        ]);

        $this->assertTrue($result['status']);

        $notification = $admin->fresh()->notifications()->firstOrFail();
        $this->assertSame('review.submitted', $notification->data['type']);
        $this->assertSame(0, $other->fresh()->notifications()->count());
    }

    public function test_approving_a_review_notifies_the_author_and_refreshes_product_rating(): void
    {
        $product = $this->makeProduct();
        $customer = $this->makeCustomer();
        $order = $this->makeCompletedOrder($customer, $product);

        $review = $product->reviews()->create([
            'user_id' => $customer->id,
            'order_id' => $order->id,
            'rating' => 4,
            'comment' => 'Pretty good.',
            'is_approved' => false,
        ]);

        app(AdminReviewService::class)->approve($review->id);

        $notification = $customer->fresh()->notifications()->firstOrFail();
        $this->assertSame('review.approved', $notification->data['type']);

        $product->refresh();
        $this->assertSame(1, $product->reviews_count);
        $this->assertEquals(4.0, (float) $product->rating);
    }

    public function test_rejecting_a_review_notifies_the_author_with_the_reason(): void
    {
        $product = $this->makeProduct();
        $customer = $this->makeCustomer();
        $order = $this->makeCompletedOrder($customer, $product);

        $review = $product->reviews()->create([
            'user_id' => $customer->id,
            'order_id' => $order->id,
            'rating' => 1,
            'comment' => 'Not as described.',
            'is_approved' => false,
        ]);

        app(AdminReviewService::class)->reject($review->id, 'Contains offensive language.');

        $notification = $customer->fresh()->notifications()->firstOrFail();
        $this->assertSame('review.rejected', $notification->data['type']);
        $this->assertSame('Contains offensive language.', $notification->data['params']['reason']);
    }
}
