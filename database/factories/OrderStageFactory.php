<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderStage;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderStageFactory extends Factory
{
    protected $model = OrderStage::class;

    public function definition(): array
    {
        return [
            'order_id'     => Order::factory(),
            'name'         => fake()->randomElement(['Cutting', 'Sewing', 'Finishing', 'Quality Check', 'Packaging', 'Printing', 'Dyeing']),
            'description'  => fake()->sentence(4),
            'status'       => fake()->randomElement(['Pending', 'In Progress', 'Completed']),
            'sort_order'   => 1,
            'completed_at' => null,
        ];
    }

    public function completed(): static
    {
        return $this->state(fn () => [
            'status'       => 'Completed',
            'completed_at' => fake()->dateTimeBetween('-2 weeks', 'now'),
        ]);
    }
}
