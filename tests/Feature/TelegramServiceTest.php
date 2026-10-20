<?php

namespace Tests\Feature;

use App\Services\TelegramService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TelegramServiceTest extends TestCase
{
    public function test_send_message_json_encodes_reply_markup(): void
    {
        config(['services.telegram.bot_token' => 'test-token']);

        Http::fake([
            'https://api.telegram.org/bottest-token/sendMessage' => Http::response(['ok' => true], 200),
        ]);

        $markup = [
            'inline_keyboard' => [
                [['text' => 'Open demo site', 'url' => 'https://demo-concierge.dev4rweb.com']],
            ],
        ];

        $result = (new TelegramService)->sendMessage('4242', 'Hello', [
            'reply_markup' => $markup,
        ]);

        $this->assertSame(['ok' => true], $result);

        Http::assertSent(function ($request) use ($markup): bool {
            return $request->url() === 'https://api.telegram.org/bottest-token/sendMessage'
                && $request['chat_id'] === '4242'
                && $request['text'] === 'Hello'
                && $request['reply_markup'] === json_encode($markup, JSON_UNESCAPED_UNICODE);
        });
    }
}
