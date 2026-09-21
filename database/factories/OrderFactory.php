<?php

namespace Database\Factories;

use App\Const\OrderConst;
use App\Const\PaymentConst;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => 'ORD' . now()->format('ymd') . Str::upper(Str::random(6)),
            'user_id' => null,
            'phone_number' => fake()->numerify('09########'),
            'email' => fake()->safeEmail(),
            'fullname' => fake()->name(),
            'address' => fake()->address(),
            'locale' => 'vi',
            'total_amount' => fake()->numberBetween(100000, 5000000),
            'status' => OrderConst::STATUS_PENDING,
            'payment_method' => PaymentConst::METHOD_COD,
            'payment_status' => PaymentConst::STATUS_UNPAID,
            'is_paid' => false,
        ];
    }

    public function cod(): static
    {
        return $this->state(fn () => ['payment_method' => PaymentConst::METHOD_COD]);
    }

    public function vnpay(): static
    {
        return $this->state(fn () => ['payment_method' => PaymentConst::METHOD_VNPAY]);
    }

    public function momo(): static
    {
        return $this->state(fn () => ['payment_method' => PaymentConst::METHOD_MOMO]);
    }

    public function bankTransfer(): static
    {
        return $this->state(fn () => ['payment_method' => PaymentConst::METHOD_BANK_TRANSFER]);
    }

    public function paid(): static
    {
        return $this->state(fn () => [
            'payment_status' => PaymentConst::STATUS_PAID,
            'is_paid' => true,
            'paid_at' => now(),
        ]);
    }

    public function status(int $status): static
    {
        return $this->state(fn () => ['status' => $status]);
    }
}
