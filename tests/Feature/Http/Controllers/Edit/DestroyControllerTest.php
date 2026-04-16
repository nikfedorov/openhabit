<?php

declare(strict_types=1);

use App\Actions\Edit\ReorderAction;
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

it('reorders remaining habits after deletion', function (): void {
    $user = User::factory()->create();

    $habit1 = Habit::factory()->daily()->create(['user_id' => $user->id, 'sort_order' => ReorderAction::MIN_SORT_ORDER]);
    $habit2 = Habit::factory()->daily()->create(['user_id' => $user->id, 'sort_order' => ReorderAction::MIN_SORT_ORDER + 1]);
    $habit3 = Habit::factory()->daily()->create(['user_id' => $user->id, 'sort_order' => ReorderAction::MIN_SORT_ORDER + 2]);

    $this->actingAs($user, 'sanctum')
        ->deleteJson('/api/edit/habits/'.$habit2->id)
        ->assertNoContent();

    // The remaining habits should be renumbered to remove the gap
    $this->assertDatabaseHas('habits', ['id' => $habit1->id, 'sort_order' => ReorderAction::MIN_SORT_ORDER]);
    $this->assertDatabaseHas('habits', ['id' => $habit3->id, 'sort_order' => ReorderAction::MIN_SORT_ORDER + 1]);
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
