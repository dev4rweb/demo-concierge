<?php

namespace Tests\Feature;

use App\Models\KnowledgeBase;
use Database\Seeders\KnowledgeBaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KnowledgeBaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_prices_are_in_usd_and_legacy_ruble_rows_are_removed(): void
    {
        KnowledgeBase::factory()->create([
            'question' => 'Сколько стоят ваши услуги?',
            'answer' => 'Простой лендинг от 30 000 руб',
            'sort_order' => 99,
        ]);

        $this->seed(KnowledgeBaseSeeder::class);

        $this->assertFalse(
            KnowledgeBase::query()->where('answer', 'like', '%руб%')->exists()
        );
        $this->assertTrue(
            KnowledgeBase::query()->where('answer', 'like', '%$2,000%')->exists()
        );
        $this->assertTrue(
            KnowledgeBase::query()->where('question', 'What services do you provide?')->exists()
        );
    }
}
