<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductVariantFactory extends Factory
{
    protected $model = ProductVariant::class;

    public function definition(): array
    {
        return [
            'product_id'     => Product::factory(),
            'variant_name'   => fake()->unique()->words(2, true),
            'sku'            => strtoupper(fake()->bothLetters(3)) . '-' . fake()->numerify('####'),
            'price'          => fake()->randomFloat(2, 500, 50000),
            'stock_quantity'  => fake()->numberBetween(50, 2000),
        ];
    }
}
