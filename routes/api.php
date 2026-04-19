<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\TelegramMiniAppController;
use App\Http\Controllers\Edit\CreateController;
use App\Http\Controllers\Edit\DestroyController;
use App\Http\Controllers\Edit\IndexController as EditIndexController;
use App\Http\Controllers\Edit\ReorderController;
use App\Http\Controllers\Edit\ShowController;
use App\Http\Controllers\Edit\StoreController;
use App\Http\Controllers\Edit\TemplateController;
use App\Http\Controllers\Edit\ToggleController as EditToggleController;
use App\Http\Controllers\Edit\ToggleFranklinController;
use App\Http\Controllers\Settings\DismissTrialBannerController;
use App\Http\Controllers\Settings\ExportController;
use App\Http\Controllers\Settings\IndexController as SettingsIndexController;
use App\Http\Controllers\Settings\UpdateController as SettingsUpdateController;
use App\Http\Controllers\StoryShareController;
use App\Http\Controllers\Track\IndexController;
use App\Http\Controllers\Track\NoteController;
use App\Http\Controllers\Track\ToggleController;
use App\Http\Controllers\View\LifeController;
use App\Http\Controllers\View\WeekController;
use App\Http\Controllers\View\YearController;
use Illuminate\Support\Facades\Route;

// Auth (unauthenticated)
Route::prefix('auth')->name('api.auth.')->group(function (): void {
    Route::post('/telegram', TelegramMiniAppController::class)->name('telegram');
});

Route::middleware('auth:sanctum')->group(function (): void {
    // Track
    Route::prefix('track')->name('api.track.')->group(function (): void {
        Route::get('/', [IndexController::class, 'show'])->name('');
        Route::post('/toggle', [ToggleController::class, 'store'])->name('toggle');
        Route::post('/daily-note', [NoteController::class, 'store'])->name('daily-note');
    });

    // View
    Route::prefix('view')->name('api.view.')->group(function (): void {
        Route::get('/week', [WeekController::class, 'show'])->name('week');
        Route::get('/year', [YearController::class, 'show'])->name('year');
        Route::get('/life', [LifeController::class, 'show'])->name('life');
    });

    // Edit
    Route::prefix('edit')->name('api.edit.')->group(function (): void {
        Route::get('/', [EditIndexController::class, 'show'])->name('');
        Route::get('/habits/create', [CreateController::class, 'show'])->name('create');
        Route::get('/habits/{habit}', [ShowController::class, 'show'])->name('show');
        Route::post('/habits', [StoreController::class, 'store'])->name('store');
        Route::put('/habits/{habit}', [StoreController::class, 'update'])->name('update');
        Route::delete('/habits/{habit}', [DestroyController::class, 'destroy'])->name('destroy');
        Route::post('/habits/{habit}/toggle', [EditToggleController::class, 'store'])->name('toggle');
        Route::post('/toggle-franklin', [ToggleFranklinController::class, 'store'])->name('toggle-franklin');
        Route::post('/habits/reorder', [ReorderController::class, 'store'])->name('reorder');
        Route::post('/templates/{habitTemplate}/copy', [TemplateController::class, 'store'])->name('templates.copy');
    });

    // Settings
    Route::prefix('settings')->name('api.settings.')->group(function (): void {
        Route::get('/', [SettingsIndexController::class, 'show'])->name('');
        Route::patch('/', [SettingsUpdateController::class, 'update'])->name('update');
        Route::post('/trial-banner/dismiss', [DismissTrialBannerController::class, 'store'])->name('trial-banner.dismiss');
        Route::post('/export', [ExportController::class, 'store'])->name('export');
    });

    // Story
    Route::post('/story/upload', StoryShareController::class)->name('api.story.upload');
});
