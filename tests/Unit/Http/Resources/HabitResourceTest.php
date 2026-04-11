<?php

declare(strict_types=1);

use App\Http\Resources\HabitResource;
use App\Models\Habit;
use Illuminate\Http\Request;

it('transforms habit model into correct structure', function (): void {
    $habit = Habit::factory()->daily()->make([
        'iterations_required' => 1,
        'sort_order' => 0,
    ]);
    $habit->setAttribute('current_iteration_for_date', 1);

    $resource = new HabitResource($habit);
    $result = $resource->toArray(new Request);

    expect($result)
        ->id->toBe($habit->id)
        ->name->toBe($habit->name)
        ->description->toBe($habit->description)
        ->iterations_required->toBe(1)
        ->is_completed->toBeTrue()
        ->current_iteration->toBe(1)
        ->sort_order->toBe(0);
});

it('defaults current_iteration to zero when not set', function (): void {
    $habit = Habit::factory()->daily()->make([
        'iterations_required' => 3,
        'sort_order' => 5,
    ]);

    $resource = new HabitResource($habit);
    $result = $resource->toArray(new Request);

    expect($result)
        ->is_completed->toBeFalse()
        ->current_iteration->toBe(0);
});
