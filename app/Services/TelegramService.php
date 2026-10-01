<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    private string $botToken;
    private string $apiUrl;

    public function __construct()
    {
        $this->botToken = config('services.telegram.bot_token');
        $this->apiUrl = "https://api.telegram.org/bot{$this->botToken}";
    }

    public function sendMessage(string $chatId, string $text, array $options = []): ?array
    {
        try {
            $response = Http::post($this->apiUrl . '/sendMessage', array_merge([
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'HTML',
            ], $options));

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Telegram sendMessage error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Telegram sendMessage exception', [
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    public function setWebhook(string $url, ?string $secretToken = null): bool
    {
        try {
            $params = ['url' => $url];
            
            if ($secretToken) {
                $params['secret_token'] = $secretToken;
            }
            
            $response = Http::post($this->apiUrl . '/setWebhook', $params);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Telegram setWebhook exception', [
                'message' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function getWebhookInfo(): ?array
    {
        try {
            $response = Http::get($this->apiUrl . '/getWebhookInfo');

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Telegram getWebhookInfo exception', [
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    public function deleteWebhook(): bool
    {
        try {
            $response = Http::post($this->apiUrl . '/deleteWebhook');
            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Telegram deleteWebhook exception', [
                'message' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
