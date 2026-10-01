<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LeadController extends Controller
{
    public function index()
    {
        $leads = Lead::with('conversation')
            ->orderByDesc('created_at')
            ->paginate(20);

        return Inertia::render('Admin/Leads', [
            'leads' => $leads,
        ]);
    }

    public function update(Request $request, Lead $lead)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'telegram_contact' => 'nullable|string|max:255',
            'needs' => 'nullable|string',
            'status' => 'required|in:new,contacted,converted,lost',
        ]);

        $lead->update($request->all());

        return back()->with('success', 'Лид обновлён');
    }
}

