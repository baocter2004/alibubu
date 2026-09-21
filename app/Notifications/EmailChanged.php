<?php

namespace App\Notifications;

use App\Const\SecurityConst;
use App\Models\Admin;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $newEmail)
    {
        $this->afterCommit();
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $prefix = $notifiable instanceof Admin ? 'admin/profile.mail.email_changed' : 'client_auth.mail.email_changed';
        $params = [
            'new_email' => $this->newEmail,
            'time' => now()->format(SecurityConst::NOTICE_TIME_FORMAT),
        ];

        return (new MailMessage)
            ->subject(__("{$prefix}.subject"))
            ->line(__("{$prefix}.line", $params))
            ->line(__("{$prefix}.warning"));
    }
}
