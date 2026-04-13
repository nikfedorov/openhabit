<?php

declare(strict_types=1);

use App\Models\Habit;
use App\Models\User;

it('requires authentication', function (): void {
    $this->postJson('/api/edit/toggle-franklin')
        ->assertUnauthorized();
});

it('deactivates all franklin habits when any are active', function (): void {
    $user = User::factory()->create();

    Habit::factory()->daily()->franklinVirtue()->create(['user_id' => $user->id, 'is_active' => true]);
    Habit::factory()->daily()->franklinVirtue()->create(['user_id' => $user->id, 'is_active' => false]);

    $this->actingAs($user, 'sanctum')
        ->postJson('/api/edit/toggle-franklin')
        ->assertNoContent();

    expect(
        Habit::query()->where('user_id', $user->id)->franklinVirtues()->where('is_active', true)->count()
    )->toBe(0);
});

it('activates all franklin habits when none are active', function (): void {
    $user = User::factory()->create();

    Habit::factory()->daily()->franklinVirtue()->count(3)->create(['user_id' => $user->id, 'is_active' => false]);

    $this->actingAs($user, 'sanctum')
        ->postJson('/api/edit/toggle-franklin')
        ->assertNoContent();

    expect(
        Habit::query()->where('user_id', $user->id)->franklinVirtues()->where('is_active', true)->count()
    )->toBe(3);
});

it('only affects authenticated user franklin habits', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $regularHabit = Habit::factory()->daily()->create(['user_id' => $user->id, 'is_active' => true]);
    Habit::factory()->daily()->franklinVirtue()->create(['user_id' => $user->id, 'is_active' => true]);
    $otherFranklinHabit = Habit::factory()->daily()->franklinVirtue()->create([
        'user_id' => $otherUser->id,
        'is_active' => true,
    ]);

    $this->actingAs($user, 'sanctum')
        ->postJson('/api/edit/toggle-franklin')
        ->assertNoContent();

    $this->assertDatabaseHas('habits', ['id' => $regularHabit->id, 'is_active' => true]);
    $this->assertDatabaseHas('habits', ['id' => $otherFranklinHabit->id, 'is_active' => true]);
});
