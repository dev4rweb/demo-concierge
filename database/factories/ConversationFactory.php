<?php

namespace Database\Factories;

use App\Models\Conversation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Conversation>
 */
class ConversationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'telegram_user_id' => (string) fake()->unique()->numerify('9########'),
            'telegram_username' => fake()->userName(),
            'telegram_first_name' => fake()->firstName(),
            'telegram_last_name' => fake()->lastName(),
            'needs_attention' => false,
            'is_active' => true,
            'last_message_at' => now(),
        ];
    }
}
