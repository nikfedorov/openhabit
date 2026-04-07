<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\TelegramMiniAppController;
use App\Http\Controllers\Telegram\WebhookController;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

Route::get('/', fn (): View => view('welcome'));

// Vue SPA shell — all frontend routes handled by Vue router
Route::get('/app', fn (): View => view('app'))->middleware('auth')->name('app');

// Telegram Mini App auth
Route::get('/telegram-miniapp', fn (): View => view('telegram-miniapp'))->name('telegram-miniapp');
Route::post('/telegram-miniapp/auth', TelegramMiniAppController::class)
    ->name('telegram-miniapp.auth')
    ->withoutMiddleware(PreventRequestForgery::class);

// App routes
Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', fn (): Response => Inertia::render('Dashboard'))->name('dashboard');
});

// Telegram webhook
Route::post('/telegram/webhook', WebhookController::class)
    ->name('telegram.webhook')
    ->withoutMiddleware(PreventRequestForgery::class);
