<?php

declare(strict_types=1);

use App\Models\Habit;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;

it('requires authentication', function (): void {
    $this->getJson('/api/edit')
        ->assertUnauthorized();
});

it('returns habits and translations', function (): void {
    $user = User::factory()->create();
    $habit = Habit::factory()->daily()->create(['user_id' => $user->id, 'sort_order' => 1]);

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/edit')
        ->assertOk()
        ->assertJson(fn (AssertableJson $json): AssertableJson => $json->has('navigationTranslations')
            ->has('settings')
            ->has('translations')
            ->has('habitTranslations')
            ->has('data', 1, fn (AssertableJson $json): AssertableJson => $json
                ->where('id', $habit->id)
                ->has('name')
                ->has('description')
                ->has('is_active')
                ->has('sort_order')
                ->has('iterations_required')
                ->has('human_readable')
                ->has('is_franklin_virtue')
            )
        );
});

it('returns all user habits including inactive and franklin virtues, excluding other users, in sort order', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $habit2 = Habit::factory()->daily()->create(['user_id' => $user->id, 'is_active' => true, 'sort_order' => 2]);
    $habit1 = Habit::factory()->daily()->create(['user_id' => $user->id, 'is_active' => false, 'sort_order' => 1]);
    Habit::factory()->daily()->franklinVirtue()->create(['user_id' => $user->id, 'sort_order' => 3]);
    Habit::factory()->daily()->create(['user_id' => $otherUser->id]);

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/edit')
        ->assertOk()
        ->assertJson(fn (AssertableJson $json): AssertableJson => $json
            ->has('data', 3)
            ->where('data.0.id', $habit1->id)
            ->where('data.1.id', $habit2->id)
            ->etc()
        );
});
