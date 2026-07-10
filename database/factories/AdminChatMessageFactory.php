<?php

namespace Database\Factories;

use App\Models\AdminChatMessage;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AdminChatMessage>
 */
class AdminChatMessageFactory extends Factory
{
    protected $model = AdminChatMessage::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->admin(),
            'body' => fake()->sentence(),
            'image_path' => null,
        ];
    }

    public function withImage(): static
    {
        return $this->state(fn (array $attributes) => [
            'body' => null,
            'image_path' => 'admin-chat/test.jpg',
        ]);
    }
}
