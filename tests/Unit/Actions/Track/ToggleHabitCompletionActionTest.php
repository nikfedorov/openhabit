<?php

declare(strict_types=1);

use App\Actions\Track\GetTrackDataAction;
use App\Actions\Track\ToggleHabitCompletionAction;
use App\Models\Habit;
use App\Models\HabitCompletion;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

it('creates a completion when none exists', function (): void {
    $user = User::factory()->create();
    $habit = Habit::factory()->daily()->for($user)->create(['iterations_required' => 1]);

    resolve(ToggleHabitCompletionAction::class)->handle($user, $habit->id, '2025-01-15');

    expect($user->habitCompletions()->count())->toBe(1)
        ->and($user->habitCompletions()->first()->current_iteration)->toBe(1);
});

it('increments the iteration when below the required count', function (): void {
    $user = User::factory()->create();
    $habit = Habit::factory()->daily()->for($user)->create(['iterations_required' => 3]);
    HabitCompletion::factory()->for($user)->for($habit)->create([
        'completed_at' => '2025-01-15',
        'current_iteration' => 1,
    ]);

    resolve(ToggleHabitCompletionAction::class)->handle($user, $habit->id, '2025-01-15');

    expect($user->habitCompletions()->first()->current_iteration)->toBe(2);
});

it('deletes the completion when reaching the required iterations again toggles it off', function (): void {
    $user = User::factory()->create();
    $habit = Habit::factory()->daily()->for($user)->create(['iterations_required' => 1]);
    HabitCompletion::factory()->for($user)->for($habit)->create([
        'completed_at' => '2025-01-15',
        'current_iteration' => 1,
    ]);

    resolve(ToggleHabitCompletionAction::class)->handle($user, $habit->id, '2025-01-15');

    expect($user->habitCompletions()->count())->toBe(0);
});

it('clears the activity cache for the user', function (): void {
    $user = User::factory()->create();
    $habit = Habit::factory()->daily()->for($user)->create();

    Cache::put(GetTrackDataAction::activityCacheKey($user), ['cached'], 60);

    resolve(ToggleHabitCompletionAction::class)->handle($user, $habit->id, '2025-01-15');

    expect(Cache::get(GetTrackDataAction::activityCacheKey($user)))->toBeNull();
});
