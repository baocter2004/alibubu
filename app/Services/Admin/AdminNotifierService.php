<?php

namespace App\Services\Admin;

use App\Models\Admin;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Throwable;

class AdminNotifierService
{
    public static function notify(Notification $notification, string $ability): void
    {
        foreach (static::recipients($ability) as $admin) {
            try {
                $admin->notify(clone $notification);
            } catch (Throwable $e) {
                Log::error('Admin notification failed', [
                    'admin_id' => $admin->getKey(),
                    'notification' => get_class($notification),
                    'ability' => $ability,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    public static function recipients(string $ability): Collection
    {
        try {
            $admins = Admin::query()->get();
        } catch (Throwable $e) {
            Log::error('Admin notification recipients lookup failed', [
                'ability' => $ability,
                'error' => $e->getMessage(),
            ]);

            return collect();
        }

        return $admins
            ->filter(fn (Admin $admin) => static::isActive($admin))
            ->filter(function (Admin $admin) use ($ability) {
                try {
                    return Gate::forUser($admin)->allows($ability);
                } catch (Throwable $e) {
                    Log::error('Admin notification ability check failed', [
                        'admin_id' => $admin->getKey(),
                        'ability' => $ability,
                        'error' => $e->getMessage(),
                    ]);

                    return false;
                }
            })
            ->values();
    }

    protected static function isActive(Admin $admin): bool
    {
        $active = $admin->getAttribute('is_active');

        return $active === null || (bool) $active;
    }
}
