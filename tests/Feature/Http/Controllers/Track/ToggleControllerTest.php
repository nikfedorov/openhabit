<?php

declare(strict_types=1);

use App\Models\Habit;
use App\Models\HabitCompletion;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;

it('creates, increments, and deletes completion through full cycle', function (): void {
    $user = User::factory()->create();
    $habit = Habit::factory()->daily()->create(['user_id' => $user->id, 'iterations_required' => 2]);
    $date = now()->toDateString();

    // First toggle → creates completion
    $this->actingAs($user, 'sanctum')
        ->postJson('/api/track/toggle', ['habit_id' => $habit->id, 'date' => $date])
        ->assertOk()
        ->assertJson(fn (AssertableJson $json): AssertableJson => $json->has('navigationTranslations')
            ->has('settings', fn (AssertableJson $json): AssertableJson => $json->has('locale')
                ->has('theme')
                ->has('moveCompletedToEnd')
            )
            ->has('habits', 1, fn (AssertableJson $json): AssertableJson => $json->where('current_iteration', 1)
                ->has('id')
                ->has('name')
                ->has('description')
                ->has('iterations_required')
                ->has('is_completed')
                ->has('sort_order')
            )
            ->has('activityData')
            ->has('data', fn (AssertableJson $json): AssertableJson => $json->where('isToday', true)
                ->where('totalHabits', 1)
                ->has('completedCount')
                ->has('date')
                ->has('dayName')
                ->has('dateFormatted')
                ->has('dailyNoteContent')
                ->has('translations')
            )
        );

    $this->assertDatabaseHas('habit_completions', [
        'habit_id' => $habit->id,
        'user_id' => $user->id,
        'current_iteration' => 1,
    ]);

    // Second toggle → increments to 2 (fully completed)
    $this->actingAs($user, 'sanctum')
        ->postJson('/api/track/toggle', ['habit_id' => $habit->id, 'date' => $date])
        ->assertOk();

    $this->assertDatabaseHas('habit_completions', [
        'habit_id' => $habit->id,
        'current_iteration' => 2,
    ]);

    // Third toggle → deletes completion (was fully completed)
    $completionId = HabitCompletion::query()->where('habit_id', $habit->id)->value('id');

    $this->actingAs($user, 'sanctum')
        ->postJson('/api/track/toggle', ['habit_id' => $habit->id, 'date' => $date])
        ->assertOk();

    $this->assertDatabaseMissing('habit_completions', ['id' => $completionId]);
});

it('rejects unauthorized and invalid requests', function (): void {
    $user = User::factory()->create();
    $otherHabit = Habit::factory()->daily()->create();
    $habit = Habit::factory()->daily()->create(['user_id' => $user->id]);

    // Other user's habit → 404
    $this->actingAs($user, 'sanctum')
        ->postJson('/api/track/toggle', ['habit_id' => $otherHabit->id, 'date' => now()->toDateString()])
        ->assertNotFound();

    // Non-existent habit → 404
    $this->actingAs($user, 'sanctum')
        ->postJson('/api/track/toggle', ['habit_id' => 99999, 'date' => now()->toDateString()])
        ->assertNotFound();

    // Future date → validation error
    $this->actingAs($user, 'sanctum')
        ->postJson('/api/track/toggle', ['habit_id' => $habit->id, 'date' => now()->addDay()->toDateString()])
        ->assertJsonValidationErrors(['date']);
});
