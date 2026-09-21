<?php

namespace Database\Factories;

use App\Models\ProductQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductQuestionFactory extends Factory
{
    protected $model = ProductQuestion::class;

    public function definition(): array
    {
        return [
            'fullname' => fake()->name(),
            'email' => fake()->safeEmail(),
            'ip_address' => fake()->ipv4(),
            'question' => fake()->sentence(10),
            'answer' => null,
            'answered_by' => null,
            'answered_at' => null,
            'is_published' => false,
        ];
    }

    public function answered(): static
    {
        return $this->state(fn () => [
            'answer' => fake()->paragraph(),
            'answered_at' => now(),
            'is_published' => true,
        ]);
    }
}
