<?php

declare(strict_types=1);

use App\Http\Resources\ViewResource;
use Illuminate\Http\Request;

test('toArray maps all resource fields', function (): void {
    $data = [
        'tab' => 'week',
        'weekStart' => '2024-01-08',
        'weekEnd' => '2024-01-14',
        'weekStartFormatted' => 'Jan 8',
        'weekEndFormatted' => 'Jan 14',
        'weekEndFormattedFull' => 'Jan 14, 2024',
        'weekYear' => '2024',
        'isCurrentWeek' => false,
        'franklinGrid' => ['week_start' => '2024-01-08', 'week_end' => '2024-01-14', 'days' => [], 'regular_habits' => [], 'franklin_habits' => []],
        'selectedYear' => 25,
        'birthdate' => '1999-01-15',
        'currentAge' => 25,
        'lifeStats' => ['currentAge' => 25, 'weeksLived' => 1300, 'yearsRemaining' => 55],
        'weeklyActivityData' => [],
        'yearlyActivityData' => [],
        'translations' => ['week' => 'Week'],
    ];

    $resource = new ViewResource($data);

    expect($resource->toArray(new Request))->toBe($data)
        ->and(ViewResource::$wrap)->toBe('data');
});
