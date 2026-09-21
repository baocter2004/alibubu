<?php

namespace App\Notifications;

use App\Const\NotificationConst;
use App\Const\QuestionConst;
use App\Models\ProductQuestion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class QuestionAnswered extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public ProductQuestion $question)
    {
        $this->afterCommit();
        $this->locale(config('app.locale'));
    }

    public function via(object $notifiable): array
    {
        return $notifiable instanceof AnonymousNotifiable ? ['mail'] : ['database', 'mail'];
    }

    public function viaConnections(): array
    {
        return ['database' => 'sync'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $params = $this->params();
        $name = $notifiable instanceof AnonymousNotifiable
            ? $this->question->askerName()
            : ($notifiable->fullname ?? $this->question->askerName());

        return (new MailMessage)
            ->subject(__('client.notifications.types.question.answered.title', $params))
            ->greeting(__('client.notifications.mail.greeting', ['name' => $name ?? '']))
            ->line(__('client.notifications.types.question.answered.body', $params))
            ->line(__('client.notifications.mail.question', ['question' => Str::limit($this->question->question, 300)]))
            ->line(__('client.notifications.mail.answer', ['answer' => Str::limit((string) $this->question->answer, 1000)]))
            ->action(__('client.notifications.mail.action'), $this->url())
            ->salutation(__('client.notifications.mail.salutation'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'question.answered',
            'url' => $this->url(),
            'icon' => 'fa-comments',
            'level' => NotificationConst::LEVEL_SUCCESS,
            'params' => $this->params(),
            'question_id' => $this->question->id,
            'product_id' => $this->question->product_id,
        ];
    }

    protected function params(): array
    {
        return [
            'product' => $this->question->product?->name ?? '-',
            'question' => Str::limit($this->question->question, QuestionConst::EXCERPT_LENGTH),
        ];
    }

    protected function url(): string
    {
        $product = $this->question->product;

        return $product
            ? route('shop.show', $product->slug) . '#questions'
            : route('shop.index');
    }
}
