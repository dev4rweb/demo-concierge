<?php

namespace Database\Factories;

use App\Models\Conversation;
use App\Models\Lead;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lead>
 */
class LeadFactory extends Factory
{
    public function definition(): array
    {
        return [
            'conversation_id' => Conversation::factory(),
            'name' => fake()->name(),
            'phone' => '+380 50 123 45 67',
            'email' => fake()->unique()->safeEmail(),
            'telegram_contact' => '@'.fake()->userName(),
            'needs' => fake()->sentence(),
            'status' => 'new',
        ];
    }
}
