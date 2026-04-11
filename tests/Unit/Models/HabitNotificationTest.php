<?php

declare(strict_types=1);

use App\Models\Habit;
use App\Models\HabitNotification;

it('belongs to habit', function (): void {
    $notification = HabitNotification::factory()->create();

    expect($notification->habit)->toBeInstanceOf(Habit::class);
});

it('has correct casts', function (): void {
    $notification = HabitNotification::factory()->create();

    expect($notification->id)->toBeInt()
        ->and($notification->habit_id)->toBeInt()
        ->and($notification->is_active)->toBeBool();
});
