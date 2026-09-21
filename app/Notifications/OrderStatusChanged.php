<?php

namespace App\Notifications;

use App\Const\NotificationConst;
use App\Const\OrderConst;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public $afterCommit = true;

    public function __construct(
        public Order $order,
        public int $from,
        public int $to,
        public ?string $note = null,
        public int $points = 0
    ) {}

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

        $mail = (new MailMessage)
            ->subject(__('client.notifications.types.order.status_changed.title', $params))
            ->greeting(__('client.notifications.mail.greeting', ['name' => $this->order->fullname]))
            ->line(__('client.notifications.types.order.status_changed.body', $params));

        if ($this->note) {
            $mail->line(__('client.notifications.mail.note', ['note' => $this->note]));
        }

        if ($this->points > 0) {
            $mail->line(__('client.notifications.mail.points_earned', ['points' => $this->points]));
        }

        return $mail
            ->action(__('client.notifications.mail.action'), $this->order->customerUrl())
            ->salutation(__('client.notifications.mail.salutation'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'order.status_changed',
            'url' => $this->order->customerUrl(),
            'icon' => OrderConst::statusIcon($this->to),
            'level' => $this->level(),
            'params' => $this->params(),
            'order_id' => $this->order->id,
        ];
    }

    protected function level(): string
    {
        return match ($this->to) {
            OrderConst::STATUS_COMPLETED => NotificationConst::LEVEL_SUCCESS,
            OrderConst::STATUS_CANCELLED, OrderConst::STATUS_DELIVERY_FAILED => NotificationConst::LEVEL_WARNING,
            OrderConst::STATUS_RETURNED => NotificationConst::LEVEL_DANGER,
            default => NotificationConst::LEVEL_INFO,
        };
    }

    protected function params(): array
    {
        return [
            'code' => $this->order->code,
            'status' => OrderConst::statusLabel($this->to),
            'points' => $this->points,
        ];
    }
}
