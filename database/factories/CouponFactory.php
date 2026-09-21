<?php

namespace Database\Factories;

use App\Const\CouponConst;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CouponFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => 'CODE' . Str::upper(Str::random(6)),
            'title' => fake()->words(2, true),
            'description' => fake()->sentence(),
            'discount_type' => CouponConst::FIX_AMOUNT,
            'discount_value' => 50000,
            'usage_limit' => 0,
            'usage_count' => 0,
            'is_expired' => false,
            'is_active' => true,
            'start_date' => null,
            'end_date' => null,
        ];
    }

    public function singleUse(): static
    {
        return $this->state(fn () => ['usage_limit' => 1]);
    }

    public function percent(int $value = 10): static
    {
        return $this->state(fn () => [
            'discount_type' => CouponConst::PERCENT,
            'discount_value' => $value,
        ]);
    }
}
