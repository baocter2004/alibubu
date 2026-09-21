<?php

namespace App\Notifications;

use App\Const\NotificationConst;
use App\Const\ReviewConst;
use App\Models\ProductReview;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class NewProductReview extends Notification implements ShouldQueue
{
    use Queueable;

    public $afterCommit = true;

    public function __construct(public ProductReview $review) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function viaConnections(): array
    {
        return ['database' => 'sync'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $params = $this->params();

        $message = (new MailMessage)
            ->subject(__('admin/notification.types.review.submitted.title', $params))
            ->greeting(__('admin/notification.mail_common.greeting', ['name' => $notifiable->name ?? '']))
            ->line(__('admin/notification.types.review.submitted.body', $params));

        if (filled($this->review->comment)) {
            $message->line('"' . Str::limit($this->review->comment, 300) . '"');
        }

        return $message->action(__('admin/notification.mail_common.action'), $this->url());
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'review.submitted',
            'url' => $this->url(),
            'icon' => 'fa-star',
            'level' => NotificationConst::LEVEL_INFO,
            'params' => $this->params(),
            'review_id' => $this->review->id,
            'product_id' => $this->review->product_id,
        ];
    }

    protected function params(): array
    {
        return [
            'product' => $this->review->product?->name ?? '-',
            'customer' => $this->review->user?->fullname ?? '-',
            'rating' => (int) $this->review->rating,
        ];
    }

    protected function url(): string
    {
        return route('admin.reviews.index', ['status' => ReviewConst::STATUS_PENDING]) . '#review-' . $this->review->id;
    }
}
