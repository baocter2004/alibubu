<?php

namespace App\Notifications;

use App\Const\NotificationConst;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class Refunded extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order)
    {
        $this->afterCommit();
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

        return (new MailMessage)
            ->subject(__('client.notifications.types.order.refunded.title', $params))
            ->greeting(__('client.notifications.mail.greeting', ['name' => $this->order->fullname]))
            ->line(__('client.notifications.types.order.refunded.body', $params))
            ->action(__('client.notifications.mail.action'), $this->order->customerUrl())
            ->salutation(__('client.notifications.mail.salutation'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'order.refunded',
            'url' => $this->order->customerUrl(),
            'icon' => 'fa-rotate-left',
            'level' => NotificationConst::LEVEL_INFO,
            'params' => $this->params(),
            'order_id' => $this->order->id,
        ];
    }

    protected function params(): array
    {
        return [
            'code' => $this->order->code,
            'total' => format_price($this->order->total_amount),
        ];
    }
}
