<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\TelegramMiniAppController;
use App\Http\Controllers\Telegram\WebhookController;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Support\Facades\Route;

Route::get('/', fn (): View => view('welcome'));

// Vue SPA shell
Route::get('/app', function (): View {
    $devToken = null;

    if (config('app.env') === 'local') {
        $user = User::query()->oldest()->first();

        if ($user !== null) {
            $user->tokens()->where('name', 'dev')->delete();
            $devToken = $user->createToken('dev')->plainTextToken;
        }
    }

    return view('app', ['devToken' => $devToken]);
})->name('app');

// Vue Router SPA catch-all — serves the Vue shell for all /app/* paths
Route::get('/app/{any}', function (): View {
    $devToken = null;

    if (config('app.env') === 'local') {
        $user = User::query()->oldest()->first();

        if ($user !== null) {
            $user->tokens()->where('name', 'dev')->delete();
            $devToken = $user->createToken('dev')->plainTextToken;
        }
    }

    return view('app', ['devToken' => $devToken]);
})->where('any', '.*')->name('app.spa');

// Telegram Mini App auth
Route::get('/telegram-miniapp', fn (): View => view('telegram-miniapp'))->name('telegram-miniapp');
Route::post('/telegram-miniapp/auth', TelegramMiniAppController::class)
    ->name('telegram-miniapp.auth')
    ->withoutMiddleware(PreventRequestForgery::class);

// Telegram webhook
Route::post('/telegram/webhook', WebhookController::class)
    ->name('telegram.webhook')
    ->withoutMiddleware(PreventRequestForgery::class);
