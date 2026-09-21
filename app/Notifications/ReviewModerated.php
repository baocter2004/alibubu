<?php

namespace App\Notifications;

use App\Const\NotificationConst;
use App\Models\ProductReview;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewModerated extends Notification implements ShouldQueue
{
    use Queueable;

    public $afterCommit = true;

    public function __construct(
        public ProductReview $review,
        public bool $approved,
        public ?string $reason = null
    ) {
        $this->locale($review->order?->getAttribute('locale') ?: config('app.locale'));
    }

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
        $key = 'client.notifications.types.' . $this->type();
        $params = $this->params();

        $message = (new MailMessage)
            ->subject(__($key . '.title', $params))
            ->greeting(__('client.notifications.mail.greeting', ['name' => $notifiable->fullname ?? '']))
            ->line(__($key . '.body', $params));

        if (filled($this->reason)) {
            $message->line(__('client.notifications.reason', ['reason' => $this->reason]));
        }

        return $message
            ->action(__('client.notifications.mail.action'), $this->url())
            ->salutation(__('client.notifications.mail.salutation'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => $this->type(),
            'url' => $this->url(),
            'icon' => $this->approved ? 'fa-star' : 'fa-comment-slash',
            'level' => $this->approved ? NotificationConst::LEVEL_SUCCESS : NotificationConst::LEVEL_WARNING,
            'params' => $this->params(),
            'review_id' => $this->review->id,
            'product_id' => $this->review->product_id,
        ];
    }

    protected function type(): string
    {
        return $this->approved ? 'review.approved' : 'review.rejected';
    }

    protected function params(): array
    {
        return array_filter([
            'product' => $this->review->product?->name ?? '-',
            'rating' => (int) $this->review->rating,
            'reason' => $this->approved ? null : $this->reason,
        ], fn ($value) => $value !== null && $value !== '');
    }

    protected function url(): string
    {
        $product = $this->review->product;

        return $product
            ? route('shop.show', $product->slug) . '#reviews'
            : route('shop.index');
    }
}
