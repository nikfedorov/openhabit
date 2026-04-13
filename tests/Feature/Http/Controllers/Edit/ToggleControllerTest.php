<?php

declare(strict_types=1);

use App\Models\Habit;
use App\Models\User;

it('requires authentication', function (): void {
    $habit = Habit::factory()->daily()->create();

    $this->postJson(sprintf('/api/edit/habits/%d/toggle', $habit->id))
        ->assertUnauthorized();
});

it('toggles habit active status', function (bool $initialState): void {
    $user = User::factory()->create();
    $habit = Habit::factory()->daily()->create([
        'user_id' => $user->id,
        'is_active' => $initialState,
    ]);

    $this->actingAs($user, 'sanctum')
        ->postJson(sprintf('/api/edit/habits/%d/toggle', $habit->id))
        ->assertNoContent();

    $this->assertDatabaseHas('habits', [
        'id' => $habit->id,
        'is_active' => ! $initialState,
    ]);
})->with([
    'active to inactive' => [true],
    'inactive to active' => [false],
]);

it('prevents toggling another user habit', function (): void {
    $user = User::factory()->create();
    $otherHabit = Habit::factory()->daily()->create();

    $this->actingAs($user, 'sanctum')
        ->postJson(sprintf('/api/edit/habits/%d/toggle', $otherHabit->id))
        ->assertForbidden();
});
