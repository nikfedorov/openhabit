<?php

declare(strict_types=1);

use App\Models\Habit;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;

it('returns 401 for unauthenticated user', function (): void {
    $this->getJson('/api/view')->assertUnauthorized();
});

it('returns view data for authenticated user', function (): void {
    $user = User::factory()->create(['birthdate' => '1990-01-15']);
    Habit::factory()->daily()->create(['user_id' => $user->id]);

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/view')
        ->assertOk()
        ->assertJson(fn (AssertableJson $json): AssertableJson => $json->has('navigationTranslations')
            ->has('settings', fn (AssertableJson $json): AssertableJson => $json->has('locale')
                ->has('theme')
                ->has('moveCompletedToEnd')
            )
            ->has('data', fn (AssertableJson $json): AssertableJson => $json->where('tab', 'week')
                ->where('isCurrentWeek', true)
                ->has('weekStart')
                ->has('weekEnd')
                ->has('weekStartFormatted')
                ->has('weekEndFormatted')
                ->has('weekEndFormattedFull')
                ->has('weekYear')
                ->has('franklinGrid', fn (AssertableJson $json): AssertableJson => $json->has('week_start')
                    ->has('week_end')
                    ->has('days')
                    ->has('regular_habits')
                    ->has('franklin_habits')
                )
                ->has('selectedYear')
                ->has('birthdate')
                ->has('currentAge')
                ->has('lifeStats')
                ->has('weeklyActivityData')
                ->has('yearlyActivityData')
                ->has('translations')
            )
        );
});

it('accepts tab, week, and year parameters', function (): void {
    $user = User::factory()->create(['birthdate' => '1990-01-15']);

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/view?tab=year&week=2024-01-08&year=5')
        ->assertOk()
        ->assertJson(fn (AssertableJson $json): AssertableJson => $json->has('data', fn (AssertableJson $json): AssertableJson => $json->where('tab', 'year')
            ->where('weekStart', '2024-01-08')
            ->where('selectedYear', 5)
            ->etc()
        )
            ->etc()
        );
});

it('validates parameters', function (string $query, string $errorField): void {
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

it('returns null life data when user has no birthdate', function (): void {
    $user = User::factory()->create(['birthdate' => null]);

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/view')
        ->assertOk()
        ->assertJson(fn (AssertableJson $json): AssertableJson => $json->has('data', fn (AssertableJson $json): AssertableJson => $json->whereNull('birthdate')
            ->whereNull('currentAge')
            ->whereNull('lifeStats')
            ->etc()
        )
            ->etc()
        );
});
