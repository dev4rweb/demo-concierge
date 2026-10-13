<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Lead;
use App\Models\Message;
use App\Services\ConversationIntakeService;
use App\Services\OpenRouterService;
use App\Services\TelegramService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramWebhookController extends Controller
{
    public function __construct(
        private TelegramService $telegramService,
        private OpenRouterService $openRouterService,
        private ConversationIntakeService $intakeService
    ) {}

    public function handle(Request $request): JsonResponse
    {
        try {
            $webhookSecret = config('services.telegram.webhook_secret');
            if ($webhookSecret) {
                $receivedSecret = $request->header('X-Telegram-Bot-Api-Secret-Token');
                if ($receivedSecret !== $webhookSecret) {
                    Log::warning('Telegram webhook: invalid secret token');

                    return response()->json(['ok' => false], 403);
                }
            }

            $update = $request->all();
            Log::info('Telegram webhook received', ['update' => $update]);

            if (! isset($update['message'])) {
                return response()->json(['ok' => true]);
            }

            $message = $update['message'];
            $chatId = $message['chat']['id'];
            $text = $message['text'] ?? '';
            $from = $message['from'] ?? [];

            $conversation = Conversation::firstOrCreate(
                ['telegram_user_id' => (string) $chatId],
                [
                    'telegram_username' => $from['username'] ?? null,
                    'telegram_first_name' => $from['first_name'] ?? null,
                    'telegram_last_name' => $from['last_name'] ?? null,
                ]
            );

            Message::create([
                'conversation_id' => $conversation->id,
                'role' => 'user',
                'content' => $text,
                'telegram_message_id' => isset($message['message_id']) ? (string) $message['message_id'] : null,
            ]);

            $conversation->update(['last_message_at' => now()]);

            $language = $this->intakeService->detectLanguage(
                $text,
                $from['language_code'] ?? null
            );

            if (strtolower(trim($text)) === '/start') {
                $welcomeMessage = $this->intakeService->welcomeMessage($language);
                $this->telegramService->sendMessage($chatId, $welcomeMessage);

                Message::create([
                    'conversation_id' => $conversation->id,
                    'role' => 'assistant',
                    'content' => $welcomeMessage,
                ]);

                return response()->json(['ok' => true]);
            }

            $keywordEscalation = $this->intakeService->mentionsHuman($text);
            $aiResult = $this->openRouterService->generateConciergeReply(
                $this->conversationHistory($conversation),
                $this->openRouterService->buildSystemPrompt($language)
            );

            $needsAttention = $keywordEscalation || ($aiResult['needs_attention'] ?? false);
            if ($needsAttention) {
                $conversation->update(['needs_attention' => true]);
            }

            $responseText = $aiResult['reply'] ?? null;
            if (! $responseText) {
                $responseText = $needsAttention
                    ? $this->intakeService->escalationMessage($language)
                    : $this->intakeService->fallbackMessage($language);
            }

            $this->telegramService->sendMessage($chatId, $responseText);

            Message::create([
                'conversation_id' => $conversation->id,
                'role' => 'assistant',
                'content' => $responseText,
            ]);

            $this->syncLead($conversation, $text);

            return response()->json(['ok' => true]);
        } catch (\Exception $e) {
            Log::error('Telegram webhook error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['ok' => true]);
        }
    }

    /**
     * @return list<array{role: string, content: string}>
     */
    private function conversationHistory(Conversation $conversation): array
    {
        return $conversation->messages()
            ->orderBy('created_at')
            ->get()
            ->map(fn (Message $msg): array => [
                'role' => $msg->role,
                'content' => $msg->content,
            ])
            ->all();
    }

    private function syncLead(Conversation $conversation, string $message): void
    {
        $fields = $this->intakeService->extractLeadFields($message);

        if (! $this->intakeService->hasContactData($fields)) {
            return;
        }

        $payload = array_filter($fields, fn ($value) => filled($value));

        $lead = $conversation->lead;
        if (! $lead) {
            Lead::create([
                'conversation_id' => $conversation->id,
                ...$payload,
            ]);

            return;
        }

        $lead->update($payload);
    }
}
