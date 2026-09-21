<?php

namespace Database\Factories;

use App\Const\ProductConst;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'branch_id' => Branch::query()->value('id') ?? Branch::create([
                'name' => 'Alibubu',
                'slug' => 'alibubu',
                'logo' => 'branches/default.png',
                'is_active' => true,
            ])->id,
            'name' => $name,
            'slug' => Str::slug($name) . '-' . fake()->unique()->numberBetween(1000, 999999),
            'short_descriptions' => fake()->sentence(),
            'descriptions' => fake()->paragraph(),
            'thumbnail' => 'products/default.png',
            'type' => ProductConst::SINGLE,
            'sku' => strtoupper(fake()->unique()->bothify('SKU-####??')),
            'price' => fake()->numberBetween(100000, 20000000),
            'sale_price' => null,
            'is_sale' => false,
            'is_featured' => false,
            'is_trending' => false,
            'is_active' => true,
            'stock' => 100,
            'sold' => 0,
        ];
    }

    public function outOfStock(): static
    {
        return $this->state(fn () => ['stock' => 0]);
    }

    public function lowStock(int $stock = 3): static
    {
        return $this->state(fn () => ['stock' => $stock]);
    }
}
