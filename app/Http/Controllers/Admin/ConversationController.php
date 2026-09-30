<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ConversationController extends Controller
{
    public function __construct(
        private TelegramService $telegramService
    ) {}

    public function show(Conversation $conversation)
    {
        $conversation->load(['messages', 'lead']);

        return Inertia::render('Admin/Conversation', [
            'conversation' => $conversation,
        ]);
    }

    public function reply(Request $request, Conversation $conversation)
    {
        $request->validate([
            'message' => 'required|string|max:4096',
        ]);

        $sent = $this->telegramService->sendMessage(
            $conversation->telegram_user_id,
            $request->message
        );

        if ($sent) {
            Message::create([
                'conversation_id' => $conversation->id,
                'role' => 'admin',
                'content' => $request->message,
            ]);

            $conversation->update([
                'needs_attention' => false,
                'last_message_at' => now(),
            ]);

            return back()->with('success', 'Сообщение отправлено');
        }

        return back()->withErrors(['message' => 'Не удалось отправить сообщение']);
    }

    public function toggleAttention(Conversation $conversation)
    {
        $conversation->update([
            'needs_attention' => !$conversation->needs_attention,
        ]);

        return back();
    }
}

