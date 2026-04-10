<?php

declare(strict_types=1);

use App\Models\Habit;
use App\Models\User;

test('show returns 401 for unauthenticated user', function (): void {
    $this->getJson('/api/view')->assertUnauthorized();
});

test('show returns view data for authenticated user', function (): void {
    $user = User::factory()->create(['birthdate' => '1990-01-15']);
    Habit::factory()->daily()->create(['user_id' => $user->id]);

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/view')
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                'tab', 'weekStart', 'weekEnd', 'weekStartFormatted', 'weekEndFormatted',
                'weekEndFormattedFull', 'weekYear', 'isCurrentWeek',
                'franklinGrid' => ['week_start', 'week_end', 'days', 'regular_habits', 'franklin_habits'],
                'selectedYear', 'birthdate', 'currentAge', 'lifeStats',
                'weeklyActivityData', 'yearlyActivityData',
                'translations',
            ],
            'navigationTranslations', 'locale',
        ])
        ->assertJsonPath('data.tab', 'week')
        ->assertJsonPath('data.isCurrentWeek', true);
});

test('show accepts tab, week, and year parameters', function (): void {
    $user = User::factory()->create(['birthdate' => '1990-01-15']);

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/view?tab=year&week=2024-01-08&year=5')
        ->assertOk()
        ->assertJsonPath('data.tab', 'year')
        ->assertJsonPath('data.weekStart', '2024-01-08')
        ->assertJsonPath('data.selectedYear', 5);
});

test('show validates parameters', function (string $query, string $errorField): void {
    $user = User::factory()->create();

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/view?'.$query)
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$errorField]);
})->with([
    'invalid tab' => ['tab=invalid', 'tab'],
    'year out of range' => ['year=100', 'year'],
    'invalid week date' => ['week=not-a-date', 'week'],
]);

test('show returns null life data when user has no birthdate', function (): void {
    $user = User::factory()->create(['birthdate' => null]);

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/view')
        ->assertOk()
        ->assertJsonPath('data.birthdate', null)
        ->assertJsonPath('data.currentAge', null)
        ->assertJsonPath('data.lifeStats', null);
});
