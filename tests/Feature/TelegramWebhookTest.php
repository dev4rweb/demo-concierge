<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Services\ConversationIntakeService;
use App\Services\OpenRouterService;
use App\Services\TelegramService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TelegramWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mock(TelegramService::class, function ($mock): void {
            $mock->shouldReceive('sendMessage')->andReturn(['ok' => true]);
        });
    }

    public function test_start_command_does_not_create_a_lead(): void
    {
        $this->postJson('/telegram/webhook', $this->telegramUpdate('/start', 'en'))
            ->assertOk();

        $this->assertDatabaseCount('conversations', 1);
        $this->assertDatabaseCount('leads', 0);
        $this->assertDatabaseHas('messages', ['role' => 'assistant']);
    }

    public function test_start_command_sends_localized_welcome_and_inline_keyboard(): void
    {
        $intake = app(ConversationIntakeService::class);
        $welcome = $intake->welcomeMessage('ru');
        $keyboard = $intake->welcomeKeyboard('ru');

        $this->mock(TelegramService::class, function ($mock) use ($welcome, $keyboard): void {
            $mock->shouldReceive('sendMessage')
                ->once()
                ->withArgs(function (int|string $chatId, string $text, array $options = []) use ($welcome, $keyboard): bool {
                    return (string) $chatId === '4242'
                        && $text === $welcome
                        && ($options['reply_markup'] ?? null) === $keyboard;
                })
                ->andReturn(['ok' => true]);
        });

        $this->postJson('/telegram/webhook', $this->telegramUpdate('/start', 'ru'))
            ->assertOk();

        $this->assertDatabaseHas('messages', [
            'role' => 'assistant',
            'content' => $welcome,
        ]);
    }

    public function test_start_falls_back_to_english_for_unknown_language_code(): void
    {
        $welcome = app(ConversationIntakeService::class)->welcomeMessage('en');

        $this->mock(TelegramService::class, function ($mock) use ($welcome): void {
            $mock->shouldReceive('sendMessage')
                ->once()
                ->withArgs(function (string $chatId, string $text, array $options = []) use ($welcome): bool {
                    return $text === $welcome
                        && isset($options['reply_markup']['inline_keyboard']);
                })
                ->andReturn(['ok' => true]);
        });

        $this->postJson('/telegram/webhook', $this->telegramUpdate('/start', 'de'))
            ->assertOk();
    }

    public function test_plain_question_does_not_create_a_lead(): void
    {
        $this->mockOpenRouter('A corporate website starts at $2,000.', false);

        $this->postJson('/telegram/webhook', $this->telegramUpdate('How much is a corporate website?', 'en'))
            ->assertOk();

        $this->assertDatabaseCount('leads', 0);
    }

    public function test_creates_lead_only_when_contact_data_is_present(): void
    {
        $this->mockOpenRouter('Thanks, I saved your contacts.', false);

        $this->postJson('/telegram/webhook', $this->telegramUpdate(
            'My name is Marina Bondarenko, phone +380 50 123 45 67, email marina.bondarenko@example.com',
            'en'
        ))->assertOk();

        $this->assertDatabaseCount('leads', 1);
        $this->assertDatabaseHas('leads', [
            'name' => 'Marina Bondarenko',
            'email' => 'marina.bondarenko@example.com',
            'phone' => '+380 50 123 45 67',
        ]);
    }

    public function test_english_manager_request_marks_needs_attention(): void
    {
        $this->mockOpenRouter('I am connecting you with a manager.', true);

        $this->postJson('/telegram/webhook', $this->telegramUpdate('Please connect me with a manager', 'en'))
            ->assertOk();

        $this->assertTrue(Conversation::query()->first()->needs_attention);
    }

    public function test_llm_flag_marks_needs_attention_without_keyword(): void
    {
        $this->mockOpenRouter('This CRM/ERP sync needs a specialist.', true);

        $this->postJson('/telegram/webhook', $this->telegramUpdate(
            'We need a two-way CRM and ERP integration with custom SSO.',
            'en'
        ))->assertOk();

        $this->assertTrue(Conversation::query()->first()->needs_attention);
        $this->assertDatabaseCount('leads', 0);
    }

    public function test_invalid_secret_is_rejected(): void
    {
        config(['services.telegram.webhook_secret' => 'secret-token']);

        $this->postJson('/telegram/webhook', $this->telegramUpdate('Hi', 'en'))
            ->assertForbidden();
    }

    /**
     * @return array<string, mixed>
     */
    private function telegramUpdate(string $text, string $languageCode): array
    {
        return [
            'message' => [
                'message_id' => 10,
                'text' => $text,
                'chat' => ['id' => 4242],
                'from' => [
                    'id' => 4242,
                    'username' => 'demo_user',
                    'first_name' => 'Demo',
                    'last_name' => 'User',
                    'language_code' => $languageCode,
                ],
            ],
        ];
    }

    private function mockOpenRouter(string $reply, bool $needsAttention): void
    {
        $this->mock(OpenRouterService::class, function ($mock) use ($reply, $needsAttention): void {
            $mock->shouldReceive('buildSystemPrompt')->andReturn('prompt');
            $mock->shouldReceive('generateConciergeReply')->andReturn([
                'reply' => $reply,
                'needs_attention' => $needsAttention,
            ]);
        });
    }
}
