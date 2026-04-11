<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;

it('returns 401 for unauthenticated user', function (): void {
    $this->getJson('/api/view/year')->assertUnauthorized();
});

it('returns year data with activity for authenticated user with birthdate', function (): void {
    $user = User::factory()->create(['birthdate' => '1990-01-15']);

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/view/year')
        ->assertOk()
        ->assertJson(fn (AssertableJson $json): AssertableJson => $json
            ->has('navigationTranslations')
            ->has('settings')
            ->has('translations')
            ->has('activityData')
            ->has('data', fn (AssertableJson $json): AssertableJson => $json
                ->has('selected')
                ->has('birthdate')
                ->has('currentAge')
            )
        );
});

it('accepts year parameter and validates it', function (): void {
    $user = User::factory()->create(['birthdate' => '1990-01-15']);

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/view/year?year=5')
        ->assertOk()
        ->assertJson(fn (AssertableJson $json): AssertableJson => $json
            ->has('data', fn (AssertableJson $json): AssertableJson => $json
                ->where('selected', 5)
                ->etc()
            )
            ->etc()
        );

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/view/year?year=100')
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['year']);
});

it('returns null data when user has no birthdate', function (): void {
    $user = User::factory()->create(['birthdate' => null]);

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/view/year')
        ->assertOk()
        ->assertJson(fn (AssertableJson $json): AssertableJson => $json
            ->has('data', fn (AssertableJson $json): AssertableJson => $json
                ->whereNull('selected')
                ->whereNull('birthdate')
                ->whereNull('currentAge')
                ->etc()
            )
            ->whereNull('activityData')
            ->etc()
        );
});
