<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KnowledgeBase;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KnowledgeBaseController extends Controller
{
    public function index()
    {
        $items = KnowledgeBase::orderBy('sort_order')
            ->orderByDesc('created_at')
            ->paginate(20);

        return Inertia::render('Admin/KnowledgeBase', [
            'items' => $items,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'category' => 'nullable|string|max:255',
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        KnowledgeBase::create($request->all());

        return back()->with('success', 'Запись добавлена');
    }

    public function update(Request $request, KnowledgeBase $knowledgeBase)
    {
        $request->validate([
            'category' => 'nullable|string|max:255',
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $knowledgeBase->update($request->all());

        return back()->with('success', 'Запись обновлена');
    }

    public function destroy(KnowledgeBase $knowledgeBase)
    {
        $knowledgeBase->delete();

        return back()->with('success', 'Запись удалена');
    }
}

