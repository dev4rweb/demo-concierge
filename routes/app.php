<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\Admin\ConversationController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KnowledgeBaseController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\GuideController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/how-to', [GuideController::class, 'index'])->name('guide');

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('conversations')->name('conversations.')->group(function (): void {
        Route::get('/{conversation}', [ConversationController::class, 'show'])->name('show');
        Route::post('/{conversation}/reply', [ConversationController::class, 'reply'])->name('reply');
        Route::post('/{conversation}/toggle-attention', [ConversationController::class, 'toggleAttention'])->name('toggle-attention');
    });

    Route::prefix('leads')->name('leads.')->group(function (): void {
        Route::get('/', [LeadController::class, 'index'])->name('index');
        Route::patch('/{lead}', [LeadController::class, 'update'])->name('update');
    });

    Route::prefix('knowledge-base')->name('knowledge-base.')->group(function (): void {
        Route::get('/', [KnowledgeBaseController::class, 'index'])->name('index');
        Route::post('/', [KnowledgeBaseController::class, 'store'])->name('store');
        Route::patch('/{knowledgeBase}', [KnowledgeBaseController::class, 'update'])->name('update');
        Route::delete('/{knowledgeBase}', [KnowledgeBaseController::class, 'destroy'])->name('destroy');
    });
});

require __DIR__.'/auth.php';
