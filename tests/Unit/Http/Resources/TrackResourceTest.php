<?php

declare(strict_types=1);

use App\Http\Resources\TrackResource;
use App\Models\Habit;
use Illuminate\Http\Request;

test('it transforms track data with habits as resources', function (): void {
    $habit1 = Habit::factory()->daily()->make([
        'iterations_required' => 1,
        'sort_order' => 0,
        'description' => null,
    ]);
    $habit1->setAttribute('current_iteration_for_date', 1);

    $habit2 = Habit::factory()->daily()->make([
        'iterations_required' => 3,
        'sort_order' => 1,
        'description' => 'Do pushups',
    ]);
    $habit2->setAttribute('current_iteration_for_date', 2);

    $data = [
        'date' => '2025-01-15',
        'dayName' => 'Wednesday',
        'dateFormatted' => 'January 15, 2025',
        'isToday' => false,
        'habits' => collect([$habit1, $habit2]),
        'totalHabits' => 2,
        'completedCount' => 1,
        'moveCompletedToEnd' => false,
        'dailyNoteContent' => 'Great day',
        'activityData' => [
            ['date' => '2025-01-15', 'percentage' => 50.0, 'completed' => 1, 'total' => 2, 'intensity' => 2],
        ],
        'translations' => ['progress' => 'Progress'],
        'navigationTranslations' => ['track' => 'Track', 'view' => 'View'],
    ];

    $resource = new TrackResource($data);
    $result = $resource->toArray(new Request);

    expect($result)
        ->date->toBe('2025-01-15')
        ->dayName->toBe('Wednesday')
        ->isToday->toBeFalse()
        ->totalHabits->toBe(2)
        ->completedCount->toBe(1)
        ->moveCompletedToEnd->toBeFalse()
        ->dailyNoteContent->toBe('Great day');

    expect($result['activityData'])->toHaveCount(1);
    expect($result['translations'])->toBe(['progress' => 'Progress']);
});

test('it does not wrap response in data key', function (): void {
    expect(TrackResource::$wrap)->toBeNull();
});
