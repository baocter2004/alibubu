<?php

namespace App\Services\Client;

use App\Const\NotificationConst;
use App\Services\BaseNotificationService;

class NotificationService extends BaseNotificationService
{
    protected function langPrefix(): string
    {
        return 'client.notifications.types';
    }

    protected function perPage(): int
    {
        return NotificationConst::CLIENT_PER_PAGE;
    }

    protected function legacyUrl(string $type, array $data): ?string
    {
        if (! empty($data['order_id'])) {
            return route('account.orders.show', $data['order_id']);
        }

        return null;
    }
}
