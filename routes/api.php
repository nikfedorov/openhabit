<?php

declare(strict_types=1);

use App\Http\Controllers\Track\IndexController;
use App\Http\Controllers\Track\NoteController;
use App\Http\Controllers\Track\ToggleController;
use App\Http\Controllers\View\IndexController as ViewIndexController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('/user', fn (Request $request) => $request->user());

    Route::get('/track', [IndexController::class, 'show'])->name('api.track');
    Route::post('/track/toggle', [ToggleController::class, 'store'])->name('api.track.toggle');
    Route::post('/track/daily-note', [NoteController::class, 'store'])->name('api.track.daily-note');

    Route::get('/view', [ViewIndexController::class, 'show'])->name('api.view');
});
