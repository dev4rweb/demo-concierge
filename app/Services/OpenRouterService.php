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
        $this->apiKey = (string) config('services.openrouter.api_key');
        $this->model = config('services.openrouter.model', 'deepseek/deepseek-chat-v3-0324:free');
        $this->baseUrl = 'https://openrouter.ai/api/v1';
    }

    public function generateResponse(array $messages, ?string $systemPrompt = null): ?string
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
                'Authorization' => 'Bearer '.$this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post($this->baseUrl.'/chat/completions', [
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

    /**
     * @param  list<array{role: string, content: string}>  $messages
     * @return array{reply: ?string, needs_attention: bool}
     */
    public function generateConciergeReply(array $messages, ?string $systemPrompt = null): array
    {
        $structuredPrompt = trim(($systemPrompt ?? '')."\n\n".$this->jsonInstructions());
        $raw = $this->generateResponse($messages, $structuredPrompt);

        if (! $raw) {
            return ['reply' => null, 'needs_attention' => false];
        }

        $parsed = $this->parseStructuredReply($raw);
        if ($parsed) {
            return $parsed;
        }

        return ['reply' => $raw, 'needs_attention' => false];
    }

    public function buildSystemPrompt(string $clientLanguage = 'en'): string
    {
        $knowledgeBase = KnowledgeBase::active()->get();

        $kbText = '';
        if ($knowledgeBase->isNotEmpty()) {
            $kbText = "\n\nCompany knowledge base:\n\n";
            foreach ($knowledgeBase as $item) {
                $kbText .= "Q: {$item->question}\nA: {$item->answer}\n\n";
            }
        }

        $languageName = $this->languageName($clientLanguage);

        return <<<PROMPT
You are the AI concierge of a business demo. You talk with visitors via Telegram.

Your tasks:
1. Answer company questions using the knowledge base. Translate facts into the client's language when needed.
2. Collect contact details (name, phone / email / Telegram, a short need description).
3. Stay polite, concise, and professional.
4. If the request is complex, conflicting, outside the knowledge base, or the client asks for a human, set needs_attention to true.

Always reply in the client's language ({$languageName}). Do not switch to another language unless the client does.
{$kbText}
PROMPT;
    }

    private function jsonInstructions(): string
    {
        return <<<'PROMPT'
Return ONLY valid JSON with this shape:
{"reply":"message for the client","needs_attention":false,"needs_attention_reason":null}

Set needs_attention to true when the client asks for a human/manager, the case is a conflict or complaint, the request is clearly outside the knowledge base, or a CRM/ERP/custom integration needs a specialist.
PROMPT;
    }

    /**
     * @return array{reply: string, needs_attention: bool}|null
     */
    private function parseStructuredReply(string $raw): ?array
    {
        $candidate = trim($raw);
        if (preg_match('/```(?:json)?\s*(\{.*\})\s*```/s', $candidate, $matches)) {
            $candidate = $matches[1];
        } elseif (preg_match('/\{.*\}/s', $candidate, $matches)) {
            $candidate = $matches[0];
        }

        $decoded = json_decode($candidate, true);
        if (! is_array($decoded) || ! isset($decoded['reply']) || ! is_string($decoded['reply'])) {
            return null;
        }

        return [
            'reply' => $decoded['reply'],
            'needs_attention' => (bool) ($decoded['needs_attention'] ?? false),
        ];
    }

    private function languageName(string $code): string
    {
        return match ($code) {
            'ru' => 'Russian',
            'uk' => 'Ukrainian',
            'zh' => 'Chinese',
            'fr' => 'French',
            'es' => 'Spanish',
            'ar' => 'Arabic',
            'pt' => 'Portuguese',
            'de' => 'German',
            'it' => 'Italian',
            default => 'English',
        };
    }
}
