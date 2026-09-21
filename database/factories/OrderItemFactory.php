<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'product_id' => Product::factory(),
            'product_variant_id' => null,
            'name' => fake()->words(3, true),
            'price' => fake()->numberBetween(100000, 5000000),
            'old_price' => null,
            'old_price_variant' => null,
            'quantity' => fake()->numberBetween(1, 3),
        ];
    }
}
