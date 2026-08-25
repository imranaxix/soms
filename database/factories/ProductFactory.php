<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'user_id'     => User::factory()->manufacturer(),
            'name'        => fake()->unique()->words(2, true),
            'description' => fake()->sentence(6),
        ];
    }
}
