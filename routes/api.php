<?php

declare(strict_types=1);

use App\Http\Controllers\Edit\DestroyController;
use App\Http\Controllers\Edit\IndexController as EditIndexController;
use App\Http\Controllers\Edit\ReorderController;
use App\Http\Controllers\Edit\ShowController;
use App\Http\Controllers\Edit\StoreController;
use App\Http\Controllers\Edit\TemplateController;
use App\Http\Controllers\Edit\ToggleController as EditToggleController;
use App\Http\Controllers\Edit\ToggleFranklinController;
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

    Route::get('/edit', [EditIndexController::class, 'show'])->name('api.edit');
    Route::get('/edit/habits/{habit}', [ShowController::class, 'show'])->name('api.edit.show');
    Route::post('/edit/habits', [StoreController::class, 'store'])->name('api.edit.store');
    Route::put('/edit/habits/{habit}', [StoreController::class, 'update'])->name('api.edit.update');
    Route::delete('/edit/habits/{habit}', [DestroyController::class, 'destroy'])->name('api.edit.destroy');
    Route::post('/edit/habits/{habit}/toggle', [EditToggleController::class, 'store'])->name('api.edit.toggle');
    Route::post('/edit/toggle-franklin', [ToggleFranklinController::class, 'store'])->name('api.edit.toggle-franklin');
    Route::post('/edit/habits/reorder', [ReorderController::class, 'store'])->name('api.edit.reorder');

    Route::post('/edit/templates/{habitTemplate}/copy', [TemplateController::class, 'store'])->name('api.edit.templates.copy');
});
