<?php

namespace App\Notifications;

use App\Const\NotificationConst;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrderPlaced extends Notification implements ShouldQueue
{
    use Queueable;

    public $afterCommit = true;

    public function __construct(public Order $order)
    {
        $this->locale(config('app.locale'));
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
        return (new MailMessage)
            ->subject(__('admin/notification.mail.subject', ['code' => $this->order->code]))
            ->greeting(__('admin/notification.mail.greeting'))
            ->line(__('admin/notification.mail.intro', [
                'code' => $this->order->code,
                'customer' => $this->order->fullname,
            ]))
            ->line(__('admin/notification.mail.total', [
                'total' => format_price($this->order->total_amount),
            ]))
            ->action(__('admin/notification.mail.action'), route('admin.orders.show', $this->order->id));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'order.placed',
            'url' => route('admin.orders.show', $this->order->id),
            'icon' => 'fa-cart-shopping',
            'level' => NotificationConst::LEVEL_INFO,
            'params' => [
                'code' => $this->order->code,
                'customer' => $this->order->fullname,
                'items' => $this->order->items()->count(),
                'total' => format_price($this->order->total_amount),
            ],
            'order_id' => $this->order->id,
        ];
    }
}
