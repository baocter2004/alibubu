<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrderPlaced extends Notification
{
    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'order.placed',
            'order_id' => $this->order->id,
            'order_code' => $this->order->code,
            'customer' => $this->order->fullname,
            'phone_number' => $this->order->phone_number,
            'total_amount' => (float) $this->order->total_amount,
            'payment_method' => (int) $this->order->payment_method,
            'items_count' => $this->order->items()->count(),
        ];
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
}
