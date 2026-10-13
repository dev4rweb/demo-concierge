<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\Lead;
use Database\Seeders\DemoConversationsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoConversationsSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_is_idempotent_and_uses_neutral_contacts(): void
    {
        $this->seed(DemoConversationsSeeder::class);
        $this->seed(DemoConversationsSeeder::class);

        $this->assertSame(6, Conversation::query()->where('telegram_user_id', 'like', 'demo-%')->count());
        $this->assertSame(1, Lead::count());

        $lead = Lead::query()->first();
        $this->assertSame('new', $lead->status);
        $this->assertSame('marina.bondarenko@example.com', $lead->email);
        $this->assertStringStartsWith('+380', $lead->phone);
        $this->assertStringNotContainsString('@mail.ru', (string) $lead->email);

        $this->assertTrue(
            Conversation::query()->where('telegram_user_id', 'demo-900003')->where('needs_attention', true)->exists()
        );
        $this->assertTrue(
            Conversation::query()->where('telegram_user_id', 'demo-900004')->where('needs_attention', false)->exists()
        );
        $this->assertDatabaseHas('messages', [
            'role' => 'admin',
        ]);
    }
}
