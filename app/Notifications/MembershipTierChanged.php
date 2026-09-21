<?php

namespace App\Notifications;

use App\Const\MembershipConst;
use App\Const\NotificationConst;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MembershipTierChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $from,
        public string $to,
        public int $points,
        ?string $locale = null
    ) {
        $this->afterCommit();
        $this->locale($locale ?: config('app.locale'));
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
        $params = $this->params();

        return (new MailMessage)
            ->subject(__('client.notifications.types.membership.tier_changed.title', $params))
            ->greeting(__('client.notifications.mail.greeting', ['name' => $notifiable->fullname ?? '']))
            ->line(__('client.notifications.types.membership.tier_changed.body', $params))
            ->action(__('client.notifications.mail.action'), route('account.profile'))
            ->salutation(__('client.notifications.mail.salutation'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'membership.tier_changed',
            'url' => route('account.profile'),
            'icon' => 'fa-award',
            'level' => $this->isPromotion() ? NotificationConst::LEVEL_SUCCESS : NotificationConst::LEVEL_INFO,
            'params' => $this->params(),
        ];
    }

    protected function isPromotion(): bool
    {
        return MembershipConst::threshold($this->to) > MembershipConst::threshold($this->from);
    }

    protected function params(): array
    {
        return [
            'from' => MembershipConst::label($this->from),
            'to' => MembershipConst::label($this->to),
            'points' => $this->points,
        ];
    }
}
