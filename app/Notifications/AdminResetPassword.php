<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Config;

class AdminResetPassword extends Notification
{
    use Queueable;

    public function __construct(public string $token) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $expire = Config::get('auth.passwords.admins.expire', 60);

        return (new MailMessage)
            ->subject('Đặt lại mật khẩu quản trị')
            ->line('Bạn nhận được email này vì có yêu cầu đặt lại mật khẩu cho tài khoản quản trị của bạn.')
            ->action('Đặt lại mật khẩu', $this->resetUrl($notifiable))
            ->line("Liên kết đặt lại mật khẩu sẽ hết hạn sau {$expire} phút.")
            ->line('Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này.');
    }

    protected function resetUrl(object $notifiable): string
    {
        return route('admin.password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);
    }
}
