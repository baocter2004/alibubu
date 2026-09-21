<?php

namespace App\Exceptions;

use RuntimeException;

class OrderStateException extends RuntimeException
{
    public function __construct(string $message = '', public readonly string $reason = 'invalid')
    {
        parent::__construct($message);
    }

    public static function notFound(): self
    {
        return new self(__('admin/order.messages.not_found'), 'not_found');
    }

    public static function invalidTransition(): self
    {
        return new self(__('admin/order.messages.invalid_transition'), 'invalid_transition');
    }

    public static function unpaid(): self
    {
        return new self(__('admin/order.messages.requires_payment'), 'unpaid');
    }

    public static function noteRequired(): self
    {
        return new self(__('admin/order.messages.note_required'), 'note_required');
    }

    public static function paymentState(string $key): self
    {
        return new self(__('admin/order.messages.' . $key), $key);
    }
}
