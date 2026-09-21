<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductVariantFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'sku' => strtoupper(fake()->unique()->bothify('VAR-####??')),
            'price' => fake()->numberBetween(100000, 20000000),
            'sale_price' => null,
            'thumbnail' => 'products/variants/default.png',
            'is_active' => true,
            'stock' => 50,
        ];
    }

    public function outOfStock(): static
    {
        return $this->state(fn () => ['stock' => 0]);
    }
}
