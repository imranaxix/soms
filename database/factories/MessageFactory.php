<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Connection;
use App\Models\Message;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition(): array
    {
        return [
            'connection_id' => Connection::factory(),
            'sender_id'     => User::factory(),
            'body'          => fake()->sentence(rand(3, 12)),
        ];
    }
}
