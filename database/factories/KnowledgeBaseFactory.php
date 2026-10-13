<?php

namespace Database\Factories;

use App\Models\KnowledgeBase;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<KnowledgeBase>
 */
class KnowledgeBaseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category' => 'Services',
            'question' => fake()->unique()->sentence(),
            'answer' => fake()->paragraph(),
            'is_active' => true,
            'sort_order' => 1,
        ];
    }
}
