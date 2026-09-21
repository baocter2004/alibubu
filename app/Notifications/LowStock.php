<?php

namespace App\Notifications;

use App\Const\NotificationConst;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class LowStock extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public array $alert)
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
            'type' => 'product.low_stock',
            'url' => route('admin.products.show', $this->alert['product_id']),
            'icon' => 'fa-box-open',
            'level' => NotificationConst::LEVEL_WARNING,
            'params' => [
                'name' => $this->alert['name'],
                'sku' => $this->alert['sku'] ?? '-',
                'stock' => (int) $this->alert['stock'],
            ],
            'product_id' => $this->alert['product_id'],
            'product_variant_id' => $this->alert['product_variant_id'] ?? null,
        ];
    }
}
