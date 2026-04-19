<?php

declare(strict_types=1);

use App\Actions\Edit\ReorderHabitsAction;
use App\Models\Habit;
use App\Models\User;

it('requires authentication', function (): void {
    $this->postJson('/api/edit/habits/reorder')
        ->assertUnauthorized();
});

it('reorders habits by updating sort_order', function (): void {
    $user = User::factory()->create();
    $habit1 = Habit::factory()->daily()->create(['user_id' => $user->id, 'sort_order' => 1]);
    $habit2 = Habit::factory()->daily()->create(['user_id' => $user->id, 'sort_order' => 2]);
    $habit3 = Habit::factory()->daily()->create(['user_id' => $user->id, 'sort_order' => 3]);

    $this->actingAs($user, 'sanctum')
        ->postJson('/api/edit/habits/reorder', [
            'ordered_ids' => [$habit3->id, $habit1->id, $habit2->id],
        ])
        ->assertNoContent();

    $this->assertDatabaseHas('habits', ['id' => $habit3->id, 'sort_order' => ReorderHabitsAction::MIN_SORT_ORDER]);
    $this->assertDatabaseHas('habits', ['id' => $habit1->id, 'sort_order' => ReorderHabitsAction::MIN_SORT_ORDER + 1]);
    $this->assertDatabaseHas('habits', ['id' => $habit2->id, 'sort_order' => ReorderHabitsAction::MIN_SORT_ORDER + 2]);
});

it('ignores habit ids not belonging to user', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $habit = Habit::factory()->daily()->create(['user_id' => $user->id, 'sort_order' => 1]);
    $otherHabit = Habit::factory()->daily()->create(['user_id' => $otherUser->id, 'sort_order' => 5]);

    $this->actingAs($user, 'sanctum')
        ->postJson('/api/edit/habits/reorder', [
            'ordered_ids' => [$otherHabit->id, $habit->id],
        ])
        ->assertNoContent();

    $this->assertDatabaseHas('habits', ['id' => $habit->id, 'sort_order' => ReorderHabitsAction::MIN_SORT_ORDER + 1]);
    $this->assertDatabaseHas('habits', ['id' => $otherHabit->id, 'sort_order' => 5]);

    $this->actingAs($user, 'sanctum')
        ->postJson('/api/edit/habits/reorder', [
            'ordered_ids' => [$otherHabit->id],
        ])
        ->assertNoContent();

    $this->assertDatabaseHas('habits', ['id' => $otherHabit->id, 'sort_order' => 5]);
});

it('validates ordered_ids is required and array', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user, 'sanctum')
        ->postJson('/api/edit/habits/reorder', [])
        ->assertJsonValidationErrors(['ordered_ids']);
});
