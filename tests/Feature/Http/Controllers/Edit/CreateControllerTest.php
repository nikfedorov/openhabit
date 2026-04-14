<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;

it('requires authentication', function (): void {
    $this->getJson('/api/edit/habits/create')
        ->assertUnauthorized();
});

it('returns translations and settings for the habit form', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/edit/habits/create')
        ->assertOk()
        ->assertJson(fn (AssertableJson $json): AssertableJson => $json
            ->has('navigationTranslations')
            ->has('settings')
            ->has('habitTranslations')
        );
});
