<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class GuideController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Guide', [
            'botUsername' => config('services.telegram.bot_username'),
        ]);
    }
}
