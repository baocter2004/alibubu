<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserResetPassword extends Notification implements ShouldQueue
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
        $expire = config('auth.passwords.users.expire', 60);

        return (new MailMessage)
            ->subject(__('client_auth.forgot.title'))
            ->line(__('client_auth.forgot.subheading'))
            ->action(__('client_auth.forgot.submit'), $this->resetUrl($notifiable))
            ->line(__('client_auth.mail.verify.expires', ['minutes' => $expire]))
            ->line(__('client_auth.mail.verify.ignore'));
    }

    protected function resetUrl(object $notifiable): string
    {
        return route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);
    }
}
