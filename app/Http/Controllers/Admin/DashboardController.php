<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Lead;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $conversations = Conversation::with(['latestMessage', 'lead'])
            ->withCount('messages')
            ->orderByDesc('last_message_at')
            ->paginate(20);

        $stats = [
            'total_conversations' => Conversation::count(),
            'needs_attention' => Conversation::where('needs_attention', true)->count(),
            'total_leads' => Lead::count(),
            'new_leads' => Lead::where('status', 'new')->count(),
        ];

        return Inertia::render('Admin/Dashboard', [
            'conversations' => $conversations,
            'stats' => $stats,
        ]);
    }
}

