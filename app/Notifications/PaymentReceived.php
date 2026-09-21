<?php

namespace App\Notifications;

use App\Const\NotificationConst;
use App\Const\PaymentConst;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class PaymentReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order, public string $gateway)
    {
        $this->afterCommit();
        $this->locale(config('app.locale'));
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function viaConnections(): array
    {
        return ['database' => 'sync'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'payment.received',
            'url' => route('admin.orders.show', $this->order->id),
            'icon' => 'fa-money-bill-wave',
            'level' => NotificationConst::LEVEL_SUCCESS,
            'params' => [
                'code' => $this->order->code,
                'total' => format_price($this->order->total_amount),
                'gateway' => PaymentConst::gatewayLabel($this->gateway),
            ],
            'order_id' => $this->order->id,
        ];
    }
}
