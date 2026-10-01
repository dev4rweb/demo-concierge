<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\TelegramWebhookController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ConversationController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\KnowledgeBaseController;
use Illuminate\Support\Facades\Route;

Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

Route::post('/telegram/webhook', [TelegramWebhookController::class, 'handle'])->name('telegram.webhook');

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::prefix('conversations')->name('conversations.')->group(function () {
        Route::get('/{conversation}', [ConversationController::class, 'show'])->name('show');
        Route::post('/{conversation}/reply', [ConversationController::class, 'reply'])->name('reply');
        Route::post('/{conversation}/toggle-attention', [ConversationController::class, 'toggleAttention'])->name('toggle-attention');
    });
    
    Route::prefix('leads')->name('leads.')->group(function () {
        Route::get('/', [LeadController::class, 'index'])->name('index');
        Route::patch('/{lead}', [LeadController::class, 'update'])->name('update');
    });
    
    Route::prefix('knowledge-base')->name('knowledge-base.')->group(function () {
        Route::get('/', [KnowledgeBaseController::class, 'index'])->name('index');
        Route::post('/', [KnowledgeBaseController::class, 'store'])->name('store');
        Route::patch('/{knowledgeBase}', [KnowledgeBaseController::class, 'update'])->name('update');
        Route::delete('/{knowledgeBase}', [KnowledgeBaseController::class, 'destroy'])->name('destroy');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

