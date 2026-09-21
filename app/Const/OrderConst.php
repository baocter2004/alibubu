<?php

namespace App\Const;

class OrderConst
{
    const STATUS_PENDING = 1;
    const STATUS_CONFIRMED = 2;
    const STATUS_SHIPPING = 3;
    const STATUS_COMPLETED = 4;
    const STATUS_CANCELLED = 5;
    const STATUS_DELIVERY_FAILED = 6;
    const STATUS_RETURNED = 7;

    const ACTOR_ADMIN = 'admin';
    const ACTOR_CUSTOMER = 'customer';
    const ACTOR_SYSTEM = 'system';
    const ACTOR_GATEWAY = 'gateway';

    const EVENT_PLACED = 'placed';
    const EVENT_STATUS_CHANGED = 'status_changed';
    const EVENT_PAYMENT_PAID = 'payment_paid';
    const EVENT_PAYMENT_FAILED = 'payment_failed';
    const EVENT_PAYMENT_PROBLEM = 'payment_problem';
    const EVENT_REFUND_PENDING = 'refund_pending';
    const EVENT_REFUNDED = 'refunded';

    const CODE_PREFIX = 'ORD';
    const CODE_RANDOM_LENGTH = 6;

    const LOW_STOCK_THRESHOLD = 5;
    const ONLINE_PAYMENT_GRACE_MINUTES = 15;
    const BANK_TRANSFER_HOLD_HOURS = 48;

    const PLACED_SESSION_KEY = 'placed_orders';
    const PLACED_SESSION_LIMIT = 10;

    const EXPIRY_CHUNK = 100;

    const ACCOUNT_PER_PAGE = 10;

    const RATE_CHECKOUT_PER_MINUTE = 5;
    const RATE_CHECKOUT_PER_HOUR = 30;
    const RATE_COUPON_PER_MINUTE = 10;
    const RATE_CART_PER_MINUTE = 60;
    const RATE_PAYMENT_RETRY_PER_MINUTE = 6;

    const LIMITER_CHECKOUT = 'checkout';
    const LIMITER_COUPON = 'coupon';
    const LIMITER_CART = 'cart';
    const LIMITER_PAYMENT_RETRY = 'payment-retry';

    public static function statuses(): array
    {
        return __('enum.order.status');
    }

    public static function statusLabel(?int $status): string
    {
        return self::statuses()[$status] ?? '-';
    }

    public static function statusBadgeClass(?int $status): string
    {
        return match ($status) {
            self::STATUS_PENDING => 'text-amber-800 bg-amber-100',
            self::STATUS_CONFIRMED => 'text-sky-700 bg-sky-100',
            self::STATUS_SHIPPING => 'text-indigo-700 bg-indigo-100',
            self::STATUS_COMPLETED => 'text-green-800 bg-green-100',
            self::STATUS_CANCELLED => 'text-red-700 bg-red-100',
            self::STATUS_DELIVERY_FAILED => 'text-orange-800 bg-orange-100',
            self::STATUS_RETURNED => 'text-slate-700 bg-slate-200',
            default => 'text-gray-700 bg-gray-100',
        };
    }

    public static function statusActionClass(?int $status): string
    {
        return match ($status) {
            self::STATUS_CONFIRMED => 'text-white bg-primary hover:bg-primary-hover',
            self::STATUS_SHIPPING => 'text-white bg-sky-600 hover:bg-sky-700',
            self::STATUS_COMPLETED => 'text-white bg-emerald-600 hover:bg-emerald-700',
            self::STATUS_CANCELLED => 'text-red-600 border border-red-200 bg-white hover:bg-red-50',
            self::STATUS_DELIVERY_FAILED => 'text-orange-700 border border-orange-200 bg-white hover:bg-orange-50',
            self::STATUS_RETURNED => 'text-slate-700 border border-slate-300 bg-white hover:bg-slate-50',
            default => 'text-gray-700 border border-gray-200 bg-white hover:bg-gray-50',
        };
    }

    public static function statusActionIcon(?int $status): string
    {
        return match ($status) {
            self::STATUS_CONFIRMED => 'fa-circle-check',
            self::STATUS_SHIPPING => 'fa-truck-fast',
            self::STATUS_COMPLETED => 'fa-flag-checkered',
            self::STATUS_CANCELLED => 'fa-ban',
            self::STATUS_DELIVERY_FAILED => 'fa-triangle-exclamation',
            self::STATUS_RETURNED => 'fa-rotate-left',
            default => 'fa-arrows-rotate',
        };
    }

    public static function statusIcon(?int $status): string
    {
        return match ($status) {
            self::STATUS_PENDING => 'fa-clock',
            self::STATUS_CONFIRMED => 'fa-clipboard-check',
            self::STATUS_SHIPPING => 'fa-truck-fast',
            self::STATUS_COMPLETED => 'fa-circle-check',
            self::STATUS_CANCELLED => 'fa-ban',
            self::STATUS_DELIVERY_FAILED => 'fa-triangle-exclamation',
            self::STATUS_RETURNED => 'fa-rotate-left',
            default => 'fa-circle',
        };
    }

    public static function progressSteps(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_CONFIRMED,
            self::STATUS_SHIPPING,
            self::STATUS_COMPLETED,
        ];
    }

    public static function progressIndex(?int $status): int
    {
        return match ($status) {
            self::STATUS_CONFIRMED => 1,
            self::STATUS_SHIPPING, self::STATUS_DELIVERY_FAILED => 2,
            self::STATUS_COMPLETED => 3,
            default => 0,
        };
    }

    public static function allowedTransitions(?int $status): array
    {
        return match ($status) {
            self::STATUS_PENDING => [self::STATUS_CONFIRMED, self::STATUS_CANCELLED],
            self::STATUS_CONFIRMED => [self::STATUS_SHIPPING, self::STATUS_CANCELLED],
            self::STATUS_SHIPPING => [self::STATUS_COMPLETED, self::STATUS_DELIVERY_FAILED],
            self::STATUS_DELIVERY_FAILED => [self::STATUS_SHIPPING, self::STATUS_RETURNED],
            self::STATUS_COMPLETED => [self::STATUS_RETURNED],
            default => [],
        };
    }

    public static function transitionsFor(?int $status, string $actorType): array
    {
        return match ($actorType) {
            self::ACTOR_ADMIN => self::allowedTransitions($status),
            self::ACTOR_CUSTOMER => self::isCancellableByCustomer($status) ? [self::STATUS_CANCELLED] : [],
            self::ACTOR_SYSTEM => $status === self::STATUS_PENDING ? [self::STATUS_CANCELLED] : [],
            default => [],
        };
    }

    public static function canTransition(?int $from, int $to, string $actorType): bool
    {
        return in_array($to, self::transitionsFor($from, $actorType), true);
    }

    public static function noteRequiredFor(int $status): bool
    {
        return in_array($status, [self::STATUS_CANCELLED, self::STATUS_DELIVERY_FAILED], true);
    }

    public static function notePanelStatuses(): array
    {
        return [self::STATUS_CANCELLED, self::STATUS_DELIVERY_FAILED, self::STATUS_RETURNED];
    }

    public static function restockStatuses(): array
    {
        return [self::STATUS_CANCELLED, self::STATUS_RETURNED];
    }

    public static function voidStatuses(): array
    {
        return [self::STATUS_CANCELLED, self::STATUS_RETURNED];
    }

    public static function isVoid(?int $status): bool
    {
        return in_array($status, self::voidStatuses(), true);
    }

    public static function isFinal(?int $status): bool
    {
        return in_array($status, [self::STATUS_CANCELLED, self::STATUS_RETURNED], true);
    }

    public static function customerCancellableStatuses(): array
    {
        return [self::STATUS_PENDING, self::STATUS_CONFIRMED];
    }

    public static function isCancellableByCustomer(?int $status): bool
    {
        return in_array($status, self::customerCancellableStatuses(), true);
    }

    public static function actorLabel(?string $actorType): string
    {
        return __('enum.order.actor.' . ($actorType ?: self::ACTOR_SYSTEM));
    }
}
