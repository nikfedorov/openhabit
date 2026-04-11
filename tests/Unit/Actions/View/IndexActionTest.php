<?php

declare(strict_types=1);

use App\Actions\View\IndexAction;
use App\Models\Habit;
use App\Models\User;

beforeEach(function (): void {
    $this->action = resolve(IndexAction::class);
});

it('returns complete data structure with defaults', function (): void {
    $user = User::factory()->create(['birthdate' => '1990-01-15']);
    Habit::factory()->daily()->create(['user_id' => $user->id]);

    $result = $this->action->handle($user);

    expect($result)
        ->toHaveKeys([
            'tab', 'weekStart', 'weekEnd', 'weekStartFormatted', 'weekEndFormatted',
            'weekEndFormattedFull', 'weekYear', 'isCurrentWeek', 'franklinGrid',
            'selectedYear', 'birthdate', 'currentAge', 'lifeStats',
            'weeklyActivityData', 'yearlyActivityData', 'translations',
        ])
        ->and($result['tab'])->toBe('week')
        ->and($result['isCurrentWeek'])->toBeTrue()
        ->and($result['selectedYear'])->toBe($result['currentAge'])
        ->and($result['franklinGrid'])->toHaveKeys(['week_start', 'week_end', 'days', 'regular_habits', 'franklin_habits'])
        ->and($result['lifeStats'])->toBeArray()
        ->and($result['translations'])->toBeArray();
});

it('respects tab, week, and year parameters', function (): void {
    $user = User::factory()->create(['birthdate' => '1990-01-15']);

    $result = $this->action->handle($user, 'year', '2024-01-08', 5);

    expect($result['tab'])->toBe('year')
        ->and($result['weekStart'])->toBe('2024-01-08')
        ->and($result['isCurrentWeek'])->toBeFalse()
        ->and($result['selectedYear'])->toBe(5);
});

it('returns null for life data without birthdate', function (): void {
    $user = User::factory()->create(['birthdate' => null]);

    $result = $this->action->handle($user);

    expect($result['birthdate'])->toBeNull()
        ->and($result['currentAge'])->toBeNull()
        ->and($result['lifeStats'])->toBeNull()
        ->and($result['weeklyActivityData'])->toBeNull()
        ->and($result['yearlyActivityData'])->toBeNull()
        ->and($result['selectedYear'])->toBeNull();
});
