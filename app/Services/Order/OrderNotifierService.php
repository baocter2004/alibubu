<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Services\Admin\AdminNotifierService;
use Closure;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Throwable;

class OrderNotifierService
{
    public function customer(Order $order, Notification $notification): void
    {
        $this->afterCommit(function () use ($order, $notification) {
            $order->loadMissing('user');
            $notification->locale($order->locale ?: config('app.locale'));

            if ($order->user) {
                $order->user->notify($notification);
            }

            $email = $order->customerEmail();

            if ($email) {
                NotificationFacade::route('mail', $email)->notify($notification);
            }
        }, $order);
    }

    public function user(object $notifiable, Notification $notification): void
    {
        $this->afterCommit(fn () => $notifiable->notify($notification));
    }

    public function admins(Notification $notification, string $ability): void
    {
        $this->afterCommit(fn () => AdminNotifierService::notify($notification, $ability));
    }

    protected function afterCommit(Closure $callback, ?Order $order = null): void
    {
        DB::afterCommit(function () use ($callback, $order) {
            try {
                $callback();
            } catch (Throwable $th) {
                Log::error(__METHOD__, [
                    'message' => $th->getMessage(),
                    'order_code' => $order?->code,
                ]);
            }
        });
    }
}
