<?php

declare(strict_types=1);

use App\Models\Habit;
use App\Models\HabitCompletion;
use App\Models\User;

test('belongs to habit', function (): void {
    $completion = HabitCompletion::factory()->create();

    expect($completion->habit)->toBeInstanceOf(Habit::class);
});

test('belongs to user', function (): void {
    $completion = HabitCompletion::factory()->create();

    expect($completion->user)->toBeInstanceOf(User::class);
});

test('casts are correct', function (): void {
    $completion = HabitCompletion::factory()->create();

    expect($completion->id)->toBeInt()
        ->and($completion->habit_id)->toBeInt()
        ->and($completion->user_id)->toBeString()
        ->and($completion->current_iteration)->toBeInt();
});
