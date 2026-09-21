<?php

namespace App\Notifications;

use App\Const\SecurityConst;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GoogleAccountLinked extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
        $this->afterCommit();
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $time = now()->format(SecurityConst::NOTICE_TIME_FORMAT);

        return (new MailMessage)
            ->subject(__('client_auth.mail.google_linked.subject'))
            ->line(__('client_auth.mail.google_linked.line', ['time' => $time]))
            ->line(__('client_auth.mail.google_linked.warning'));
    }
}
