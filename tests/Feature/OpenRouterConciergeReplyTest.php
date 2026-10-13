<?php

namespace Tests\Feature;

use App\Services\OpenRouterService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OpenRouterConciergeReplyTest extends TestCase
{
    use RefreshDatabase;

    public function test_parses_needs_attention_flag_from_json_reply(): void
    {
        config(['services.openrouter.api_key' => 'test-key']);

        Http::fake([
            'https://openrouter.ai/api/v1/chat/completions' => Http::response([
                'choices' => [[
                    'message' => [
                        'content' => '{"reply":"A specialist will join.","needs_attention":true}',
                    ],
                ]],
            ], 200),
        ]);

        $result = app(OpenRouterService::class)->generateConciergeReply([
            ['role' => 'user', 'content' => 'We need CRM/ERP sync'],
        ], 'prompt');

        $this->assertSame('A specialist will join.', $result['reply']);
        $this->assertTrue($result['needs_attention']);
    }
}
