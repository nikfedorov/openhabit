<?php

declare(strict_types=1);

use App\Models\User;
use App\Services\LifeGridService;
use Illuminate\Testing\Fluent\AssertableJson;

it('returns 401 for unauthenticated user', function (): void {
    $this->getJson('/api/view/life')->assertUnauthorized();
});

it('returns life data with activity for authenticated user with birthdate', function (): void {
    $user = User::factory()->create(['birthdate' => '1990-01-15']);

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/view/life')
        ->assertOk()
        ->assertJson(fn (AssertableJson $json): AssertableJson => $json
            ->has('navigationTranslations')
            ->has('settings')
            ->has('translations')
            ->has('activityData', LifeGridService::TOTAL_LIFE_YEARS)
            ->has('data', fn (AssertableJson $json): AssertableJson => $json
                ->has('birthdate')
                ->has('currentAge')
                ->has('weeksLived')
                ->has('yearsRemaining')
            )
        );
});

it('returns null data when user has no birthdate', function (): void {
    $user = User::factory()->create(['birthdate' => null]);

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/view/life')
        ->assertOk()
        ->assertJson(fn (AssertableJson $json): AssertableJson => $json
            ->has('data', fn (AssertableJson $json): AssertableJson => $json
                ->whereNull('birthdate')
                ->whereNull('currentAge')
                ->whereNull('weeksLived')
                ->whereNull('yearsRemaining')
                ->etc()
            )
            ->whereNull('activityData')
            ->etc()
        );
});
