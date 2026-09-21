<?php

namespace App\Notifications;

use App\Const\SecurityConst;
use App\Models\Admin;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public $afterCommit = true;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $prefix = $notifiable instanceof Admin ? 'admin/profile.mail.password_changed' : 'client_auth.mail.password_changed';
        $time = now()->format(SecurityConst::NOTICE_TIME_FORMAT);

        return (new MailMessage)
            ->subject(__("{$prefix}.subject"))
            ->line(__("{$prefix}.line", ['time' => $time]))
            ->line(__("{$prefix}.warning"));
    }
}
