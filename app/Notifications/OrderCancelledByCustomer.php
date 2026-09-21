<?php

namespace App\Notifications;

use App\Const\NotificationConst;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderCancelledByCustomer extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order, public ?string $reason = null)
    {
        $this->afterCommit();
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
        $params = $this->params();

        return (new MailMessage)
            ->subject(__('admin/notification.types.order.cancelled_by_customer.title', $params))
            ->greeting(__('admin/notification.mail_common.greeting', ['name' => $notifiable->name ?? '']))
            ->line(__('admin/notification.types.order.cancelled_by_customer.body', $params))
            ->action(__('admin/notification.mail_common.action'), route('admin.orders.show', $this->order->id));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'order.cancelled_by_customer',
            'url' => route('admin.orders.show', $this->order->id),
            'icon' => 'fa-ban',
            'level' => NotificationConst::LEVEL_WARNING,
            'params' => $this->params(),
            'order_id' => $this->order->id,
        ];
    }

    protected function params(): array
    {
        return [
            'code' => $this->order->code,
            'customer' => $this->order->fullname,
            'reason' => $this->reason ?: '-',
        ];
    }
}
