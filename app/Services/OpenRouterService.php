<?php

namespace App\Services;

use App\Models\KnowledgeBase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenRouterService
{
    private string $apiKey;
    private string $model;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.openrouter.api_key');
        $this->model = config('services.openrouter.model', 'deepseek/deepseek-chat-v3-0324:free');
        $this->baseUrl = 'https://openrouter.ai/api/v1';
    }

    public function generateResponse(array $messages, string $systemPrompt = null): ?string
    {
        try {
            $requestMessages = [];

            if ($systemPrompt) {
                $requestMessages[] = [
                    'role' => 'system',
                    'content' => $systemPrompt,
                ];
            }

            foreach ($messages as $message) {
                $requestMessages[] = [
                    'role' => $message['role'] === 'admin' ? 'assistant' : $message['role'],
                    'content' => $message['content'],
                ];
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post($this->baseUrl . '/chat/completions', [
                'model' => $this->model,
                'messages' => $requestMessages,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['choices'][0]['message']['content'] ?? null;
            }

            Log::error('OpenRouter API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('OpenRouter exception', [
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    public function buildSystemPrompt(): string
    {
        $knowledgeBase = KnowledgeBase::active()->get();
        
        $kbText = '';
        if ($knowledgeBase->isNotEmpty()) {
            $kbText = "\n\nБаза знаний компании:\n\n";
            foreach ($knowledgeBase as $item) {
                $kbText .= "Q: {$item->question}\nA: {$item->answer}\n\n";
            }
        }

        return <<<PROMPT
Вы — AI-консьерж демо-приложения для бизнеса. Вы общаетесь с посетителями через Telegram.

Ваши задачи:
1. Отвечать на вопросы о компании на основе базы знаний
2. Собирать контактную информацию (имя, телефон/email/Telegram, краткое описание потребности)
3. Быть вежливым и профессиональным
4. Если пользователь просит связаться с человеком, сообщите, что его запрос будет передан администратору

Общайтесь на русском языке, будьте краткими и по делу.
{$kbText}
PROMPT;
    }
}
