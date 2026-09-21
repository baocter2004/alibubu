<?php

namespace Database\Factories;

use App\Const\AdminConst;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => AdminConst::ROLE_STAFF,
            'is_active' => true,
            'remember_token' => Str::random(10),
        ];
    }

    public function superAdmin(): static
    {
        return $this->state(fn () => ['role' => AdminConst::ROLE_SUPER_ADMIN]);
    }

    public function manager(): static
    {
        return $this->state(fn () => ['role' => AdminConst::ROLE_MANAGER]);
    }

    public function staff(): static
    {
        return $this->state(fn () => ['role' => AdminConst::ROLE_STAFF]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
