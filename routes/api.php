<?php

declare(strict_types=1);

use App\Http\Controllers\Track\IndexController;
use App\Http\Controllers\Track\NoteController;
use App\Http\Controllers\Track\ToggleController;
use App\Http\Controllers\View\LifeController;
use App\Http\Controllers\View\WeekController;
use App\Http\Controllers\View\YearController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('/track', [IndexController::class, 'show'])->name('api.track');
    Route::post('/track/toggle', [ToggleController::class, 'store'])->name('api.track.toggle');
    Route::post('/track/daily-note', [NoteController::class, 'store'])->name('api.track.daily-note');

    Route::get('/view/week', [WeekController::class, 'show'])->name('api.view.week');
    Route::get('/view/year', [YearController::class, 'show'])->name('api.view.year');
    Route::get('/view/life', [LifeController::class, 'show'])->name('api.view.life');
});
