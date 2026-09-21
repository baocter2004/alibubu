<?php

namespace App\Notifications;

use App\Const\NotificationConst;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentFailed extends Notification implements ShouldQueue
{
    use Queueable;

    public $afterCommit = true;

    public function __construct(public Order $order) {}

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

        return (new MailMessage)
            ->subject(__('client.notifications.types.order.payment_failed.title', $params))
            ->greeting(__('client.notifications.mail.greeting', ['name' => $this->order->fullname]))
            ->line(__('client.notifications.types.order.payment_failed.body', $params))
            ->action(__('client.notifications.mail.action'), $this->order->customerUrl())
            ->salutation(__('client.notifications.mail.salutation'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'order.payment_failed',
            'url' => $this->order->customerUrl(),
            'icon' => 'fa-triangle-exclamation',
            'level' => NotificationConst::LEVEL_WARNING,
            'params' => $this->params(),
            'order_id' => $this->order->id,
        ];
    }

    protected function params(): array
    {
        return [
            'code' => $this->order->code,
        ];
    }
}
