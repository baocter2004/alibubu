<?php

namespace App\Notifications;

use App\Const\NotificationConst;
use App\Const\OrderConst;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RefundRequired extends Notification implements ShouldQueue
{
    use Queueable;

    public $afterCommit = true;

    public function __construct(public Order $order, public int $status)
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
        $params = $this->params();

        return (new MailMessage)
            ->subject(__('admin/notification.types.refund.required.title', $params))
            ->greeting(__('admin/notification.mail_common.greeting', ['name' => $notifiable->name ?? '']))
            ->line(__('admin/notification.types.refund.required.body', $params))
            ->action(__('admin/notification.mail_common.action'), route('admin.orders.show', $this->order->id));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'refund.required',
            'url' => route('admin.orders.show', $this->order->id),
            'icon' => 'fa-hand-holding-dollar',
            'level' => NotificationConst::LEVEL_DANGER,
            'params' => $this->params(),
            'order_id' => $this->order->id,
        ];
    }

    protected function params(): array
    {
        return [
            'code' => $this->order->code,
            'status' => OrderConst::statusLabel($this->status),
            'total' => format_price($this->order->total_amount),
        ];
    }
}
