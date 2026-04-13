<?php

declare(strict_types=1);

use App\Models\Habit;
use App\Models\HabitCompletion;
use App\Models\User;

it('requires authentication', function (): void {
    $habit = Habit::factory()->daily()->create();

    $this->deleteJson('/api/edit/habits/'.$habit->id)
        ->assertUnauthorized();
});

it('force deletes a habit without completions and soft deletes one with completions', function (): void {
    $user = User::factory()->create();

    $habitWithout = Habit::factory()->daily()->create(['user_id' => $user->id]);
    $habitWith = Habit::factory()->daily()->create(['user_id' => $user->id]);
    HabitCompletion::factory()->create([
        'habit_id' => $habitWith->id,
        'user_id' => $user->id,
        'completed_at' => now()->toDateString(),
    ]);

    $this->actingAs($user, 'sanctum')
        ->deleteJson('/api/edit/habits/'.$habitWithout->id)
        ->assertNoContent();

    $this->assertDatabaseMissing('habits', ['id' => $habitWithout->id]);

    $this->actingAs($user, 'sanctum')
        ->deleteJson('/api/edit/habits/'.$habitWith->id)
        ->assertNoContent();

    $this->assertSoftDeleted('habits', ['id' => $habitWith->id]);
});

it('prevents unauthorized deletion', function (): void {
    $user = User::factory()->create();
    $otherHabit = Habit::factory()->daily()->create();
    $franklinHabit = Habit::factory()->daily()->franklinVirtue()->create(['user_id' => $user->id]);

    $this->actingAs($user, 'sanctum')
        ->deleteJson('/api/edit/habits/'.$otherHabit->id)
        ->assertForbidden();

    $this->actingAs($user, 'sanctum')
        ->deleteJson('/api/edit/habits/'.$franklinHabit->id)
        ->assertForbidden();

    $this->assertDatabaseHas('habits', ['id' => $franklinHabit->id]);
});
