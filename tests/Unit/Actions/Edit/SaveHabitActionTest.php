<?php

declare(strict_types=1);

use App\Actions\Edit\SaveHabitAction;
use App\Models\Habit;
use App\Models\HabitNotification;
use App\Models\User;

/**
 * @return array<string, mixed>
 */
function saveHabitActionData(array $overrides = []): array
{
    return array_merge([
        'name' => 'Test habit',
        'description' => null,
        'frequency' => 'DAILY',
        'iterations_required' => 1,
        'is_active' => true,
        'weekly_days' => [],
        'monthly_days' => [],
        'monthly_mode' => 'day',
        'monthly_position' => 1,
        'monthly_weekday' => 0,
        'notifications' => [],
    ], $overrides);
}

it('creates a daily habit', function (): void {
    $user = User::factory()->create();

    $habit = resolve(SaveHabitAction::class)->handle($user, saveHabitActionData([
        'name' => 'Run',
        'description' => '5km',
    ]));

    expect($habit->user_id)->toBe($user->id)
        ->and($habit->rrule)->toBe('FREQ=DAILY')
        ->and($habit->getTranslation('name', 'en'))->toBe('Run')
        ->and($habit->getTranslation('description', 'en'))->toBe('5km');
});

it('builds the correct rrule from frequency data', function (array $overrides, string $expected): void {
    $user = User::factory()->create();

    $habit = resolve(SaveHabitAction::class)->handle($user, saveHabitActionData($overrides));

    expect($habit->rrule)->toBe($expected);
})->with([
    'weekly with days' => [['frequency' => 'WEEKLY', 'weekly_days' => [0, 2, 4]], 'FREQ=WEEKLY;BYDAY=MO,WE,FR'],
    'weekly without days falls back to daily' => [['frequency' => 'WEEKLY', 'weekly_days' => []], 'FREQ=DAILY'],
    'monthly by day' => [['frequency' => 'MONTHLY', 'monthly_mode' => 'day', 'monthly_days' => [15, 1]], 'FREQ=MONTHLY;BYMONTHDAY=1,15'],
    'monthly by position' => [['frequency' => 'MONTHLY', 'monthly_mode' => 'position', 'monthly_position' => 2, 'monthly_weekday' => 1], 'FREQ=MONTHLY;BYDAY=2TU'],
    'unknown frequency falls back to daily' => [['frequency' => 'UNKNOWN'], 'FREQ=DAILY'],
]);

it('updates an existing habit and replaces notifications, deduping by time', function (): void {
    $user = User::factory()->create();
    $habit = Habit::factory()->daily()->for($user)->create();
    HabitNotification::factory()->create(['habit_id' => $habit->id, 'time' => '09:00']);

    $updated = resolve(SaveHabitAction::class)->handle($user, saveHabitActionData([
        'name' => 'Updated',
        'description' => '',
        'frequency' => 'WEEKLY',
        'weekly_days' => [0, 4],
        'iterations_required' => 3,
        'is_active' => false,
        'notifications' => [
            ['time' => '08:00', 'is_active' => true],
            ['time' => '08:00', 'is_active' => false],
            ['time' => '20:00', 'is_active' => true],
        ],
    ]), $habit);

    expect($updated->id)->toBe($habit->id)
        ->and($updated->rrule)->toBe('FREQ=WEEKLY;BYDAY=MO,FR')
        ->and($updated->iterations_required)->toBe(3)
        ->and($updated->is_active)->toBeFalse()
        ->and($updated->description)->toBeEmpty();

    expect($habit->notifications()->pluck('time')->all())->toBe(['08:00', '20:00']);
});
