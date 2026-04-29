<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\TelegramWidgetController;
use App\Http\Controllers\Settings\ExportDownloadController;
use App\Http\Controllers\Telegram\WebhookController;
use App\Http\Middleware\InjectDevToken;
use App\Models\Setting;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Support\Facades\Route;

Route::get('/', fn (): View => view('welcome', [
    'botUsername' => Setting::botUsername(),
    'trackingScripts' => Setting::trackingScripts(),
    'isLoggedIn' => auth()->check(),
]))->name('welcome');

// Telegram Login Widget callback (browser redirect with auth params).
Route::get('/auth/telegram/callback', TelegramWidgetController::class)
    ->name('auth.telegram.callback');

// Export data download (signed URL, no auth required)
Route::get('/export/{filename}', ExportDownloadController::class)
    ->where('filename', 'user-[0-9a-f-]+-\d{8}-\d{6}\.zip')
    ->middleware('signed')
    ->name('export.download');

// Vue SPA shell
Route::middleware(InjectDevToken::class)->group(function (): void {
    Route::get('/app', fn (): View => view('app'))->name('app');
    Route::get('/app/{any}', fn (): View => view('app'))->where('any', '.*')->name('app.spa');
});

// Telegram Mini App auth
Route::get('/telegram-miniapp', fn (): View => view('telegram-miniapp'))->name('telegram-miniapp');

// Telegram webhook
Route::post('/telegram/webhook', WebhookController::class)
    ->name('telegram.webhook')
    ->withoutMiddleware(PreventRequestForgery::class);
