<?php

declare(strict_types=1);

use App\Models\Habit;
use App\Notifications\HabitReminderNotification;

test('via returns database channel', function (): void {
    $habit = Habit::factory()->create();
    $notification = new HabitReminderNotification($habit);

    expect($notification->via(new stdClass))->toBe(['database']);
});

test('stores habit', function (): void {
    $habit = Habit::factory()->create();
    $notification = new HabitReminderNotification($habit);

    expect($notification->habit->id)->toBe($habit->id);
});
