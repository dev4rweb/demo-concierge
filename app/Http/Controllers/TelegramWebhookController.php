<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Lead;
use App\Services\OpenRouterService;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramWebhookController extends Controller
{
    public function __construct(
        private TelegramService $telegramService,
        private OpenRouterService $openRouterService
    ) {}

    public function handle(Request $request)
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

            if (!isset($update['message'])) {
                return response()->json(['ok' => true]);
            }

            $message = $update['message'];
            $chatId = $message['chat']['id'];
            $text = $message['text'] ?? '';
            $from = $message['from'];

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
                'telegram_message_id' => (string) $message['message_id'],
            ]);

            $conversation->update(['last_message_at' => now()]);

            if (strtolower($text) === '/start') {
                $welcomeMessage = "Здравствуйте! 👋\n\n"
                    . "Я AI-консьерж демо-приложения. Я могу:\n"
                    . "• Ответить на вопросы о компании\n"
                    . "• Собрать вашу контактную информацию\n"
                    . "• Связать вас с администратором\n\n"
                    . "Чем могу помочь?";

                $this->telegramService->sendMessage($chatId, $welcomeMessage);

                Message::create([
                    'conversation_id' => $conversation->id,
                    'role' => 'assistant',
                    'content' => $welcomeMessage,
                ]);

                return response()->json(['ok' => true]);
            }

            if (stripos($text, 'человек') !== false || stripos($text, 'администратор') !== false || stripos($text, 'менеджер') !== false) {
                $conversation->update(['needs_attention' => true]);
                
                $responseText = "Понял! Ваш запрос передан администратору. "
                    . "С вами свяжутся в ближайшее время. "
                    . "Можете оставить свои контактные данные для связи.";

                $this->telegramService->sendMessage($chatId, $responseText);

                Message::create([
                    'conversation_id' => $conversation->id,
                    'role' => 'assistant',
                    'content' => $responseText,
                ]);

                return response()->json(['ok' => true]);
            }

            $conversationMessages = $conversation->messages()
                ->orderBy('created_at')
                ->get()
                ->map(fn($msg) => [
                    'role' => $msg->role,
                    'content' => $msg->content,
                ])
                ->toArray();

            $systemPrompt = $this->openRouterService->buildSystemPrompt();
            $aiResponse = $this->openRouterService->generateResponse($conversationMessages, $systemPrompt);

            if ($aiResponse) {
                $this->telegramService->sendMessage($chatId, $aiResponse);

                Message::create([
                    'conversation_id' => $conversation->id,
                    'role' => 'assistant',
                    'content' => $aiResponse,
                ]);

                $this->extractLeadInfo($conversation, $text);
            } else {
                $fallbackMessage = "Извините, произошла ошибка. Попробуйте позже или попросите связаться с администратором.";
                $this->telegramService->sendMessage($chatId, $fallbackMessage);
            }

            return response()->json(['ok' => true]);
        } catch (\Exception $e) {
            Log::error('Telegram webhook error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['ok' => true]);
        }
    }

    private function extractLeadInfo(Conversation $conversation, string $message): void
    {
        $lead = $conversation->lead;
        if (!$lead) {
            $lead = Lead::create([
                'conversation_id' => $conversation->id,
            ]);
        }

        if (preg_match('/[\w\.\-]+@[\w\.\-]+\.\w+/', $message, $matches)) {
            $lead->update(['email' => $matches[0]]);
        }

        if (preg_match('/\+?\d[\d\s\-\(\)]{8,}/', $message, $matches)) {
            $lead->update(['phone' => $matches[0]]);
        }

        if (preg_match('/[Мм]еня зовут\s+(.+?)[\.\,\n]/', $message, $matches)) {
            $lead->update(['name' => trim($matches[1])]);
        }
    }
}

