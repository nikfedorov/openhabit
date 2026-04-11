<?php

declare(strict_types=1);

use App\Models\Habit;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;

it('returns 401 for unauthenticated user', function (): void {
    $this->getJson('/api/view/week')->assertUnauthorized();
});

it('returns week data with correct structure and defaults to current week', function (): void {
    $user = User::factory()->create();
    Habit::factory()->daily()->create(['user_id' => $user->id]);

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/view/week')
        ->assertOk()
        ->assertJson(fn (AssertableJson $json): AssertableJson => $json
            ->has('navigationTranslations')
            ->has('settings')
            ->has('translations')
            ->has('days')
            ->has('habits')
            ->has('data', fn (AssertableJson $json): AssertableJson => $json
                ->where('isCurrent', true)
                ->has('start')
                ->has('end')
                ->has('startFormatted')
                ->has('endFormatted')
                ->has('endFormattedFull')
                ->has('year')
            )
        );
});

it('accepts week parameter and validates it', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/view/week?week=2024-01-08')
        ->assertOk()
        ->assertJson(fn (AssertableJson $json): AssertableJson => $json
            ->has('data', fn (AssertableJson $json): AssertableJson => $json
                ->where('start', '2024-01-08')
                ->where('isCurrent', false)
                ->etc()
            )
            ->etc()
        );

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/view/week?week=not-a-date')
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['week']);
});
