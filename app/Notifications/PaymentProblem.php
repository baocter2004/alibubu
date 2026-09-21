<?php

namespace App\Notifications;

use App\Const\NotificationConst;
use App\Const\PaymentConst;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentProblem extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Order $order,
        public string $problem,
        public string $gateway,
        public float $amount
    ) {
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
            ->subject(__('admin/notification.types.payment.problem.title', $params))
            ->greeting(__('admin/notification.mail_common.greeting', ['name' => $notifiable->name ?? '']))
            ->line(__('admin/notification.types.payment.problem.body', $params))
            ->line(__('admin/order.problems.' . $this->problem))
            ->action(__('admin/notification.mail_common.action'), route('admin.orders.show', $this->order->id));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'payment.problem',
            'url' => route('admin.orders.show', $this->order->id),
            'icon' => 'fa-triangle-exclamation',
            'level' => NotificationConst::LEVEL_DANGER,
            'params' => $this->params(),
            'order_id' => $this->order->id,
        ];
    }

    protected function params(): array
    {
        return [
            'code' => $this->order->code,
            'gateway' => PaymentConst::gatewayLabel($this->gateway),
            'amount' => format_price($this->amount),
            'problem' => __('admin/order.problems.' . $this->problem),
        ];
    }
}
