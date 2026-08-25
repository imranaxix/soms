<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'order_id'    => Order::factory(),
            'payer_id'    => User::factory()->create(['role' => 'shop_owner']),
            'payee_id'    => User::factory()->create(['role' => 'manufacturer']),
            'amount'      => fake()->randomFloat(2, 1000, 250000),
            'txn_ref_no'  => 'TXN-' . strtoupper(fake()->bothLetters(4)) . fake()->numerify('######'),
            'status'      => 'completed',
            'paid_at'     => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }

    public function completed(): static
    {
        return $this->state(fn () => [
            'status'  => 'completed',
            'paid_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn () => [
            'status'  => 'pending',
            'paid_at' => null,
        ]);
    }

    public function viaStripe(): static
    {
        return $this->state(fn () => [
            'stripe_payment_intent_id' => 'pi_' . Str::random(24),
        ]);
    }

    public function viaSafepay(): static
    {
        return $this->state(fn () => [
            'safepay_tracker_id' => 'sp_' . Str::random(16),
        ]);
    }
}
