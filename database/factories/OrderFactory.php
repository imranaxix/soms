<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $total = fake()->randomFloat(2, 5000, 500000);

        return [
            'order_number'       => 'ORD-' . fake()->unique()->numerify('1000'),
            'shop_owner_id'      => User::factory()->create(['role' => 'shop_owner']),
            'manufacturer_id'    => User::factory()->create(['role' => 'manufacturer']),
            'product_id'         => Product::factory(),
            'quantity'           => fake()->numberBetween(10, 500),
            'unit'               => fake()->randomElement(['pcs', 'kg', 'dozen', 'sets']),
            'total_amount'       => $total,
            'paid_amount'        => 0,
            'payment_terms'      => fake()->randomElement(['50% advance', 'Net 30', 'Full upfront', 'Upon delivery']),
            'due_date'           => fake()->dateTimeBetween('+1 week', '+2 months'),
            'status'             => 'Pending',
            'progress_percent'   => 0,
            'special_instructions' => fake()->optional(0.6)->sentence(),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => [
            'status'           => 'Pending',
            'progress_percent' => 0,
            'paid_amount'      => 0,
        ]);
    }

    public function inProgress(): static
    {
        return $this->state(fn () => [
            'status'           => 'In Progress',
            'progress_percent' => fake()->numberBetween(20, 80),
        ]);
    }

    public function delivered(): static
    {
        return $this->state(fn () => [
            'status'           => 'Delivered',
            'progress_percent' => 100,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn () => [
            'status'           => 'Completed',
            'progress_percent' => 100,
        ]);
    }

    public function partiallyPaid(): static
    {
        return $this->state(function () {
            $total = $this->faker->randomFloat(2, 5000, 500000);
            return [
                'total_amount' => $total,
                'paid_amount'  => round($total * fake()->randomFloat(2, 0.2, 0.7), 2),
            ];
        });
    }

    public function fullyPaid(): static
    {
        return $this->state(fn () => [
            'paid_amount' => fn (array $attr) => $attr['total_amount'],
        ]);
    }
}
