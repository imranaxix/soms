<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Connection;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConnectionFactory extends Factory
{
    protected $model = Connection::class;

    public function definition(): array
    {
        $shopOwner   = User::factory()->create(['role' => 'shop_owner']);
        $manufacturer = User::factory()->create(['role' => 'manufacturer']);

        return [
            'shop_owner_id'   => $shopOwner->id,
            'manufacturer_id' => $manufacturer->id,
            'initiated_by'    => $shopOwner->id,
            'status'          => 'accepted',
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => [
            'status' => 'pending',
        ]);
    }

    public function accepted(): static
    {
        return $this->state(fn () => [
            'status' => 'accepted',
        ]);
    }
}
