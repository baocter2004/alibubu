<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminResetPassword extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $token)
    {
        $this->afterCommit();
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $expire = config('auth.passwords.admins.expire', 60);

        return (new MailMessage)
            ->subject(__('admin/auth.mail.reset_password.subject'))
            ->line(__('admin/auth.mail.reset_password.line'))
            ->action(__('admin/auth.mail.reset_password.action'), $this->resetUrl($notifiable))
            ->line(__('admin/auth.mail.reset_password.expires', ['minutes' => $expire]))
            ->line(__('admin/auth.mail.reset_password.ignore'));
    }

    protected function resetUrl(object $notifiable): string
    {
        return route('admin.password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);
    }
}
