<?php

declare(strict_types=1);

use App\Enums\StatPeriod;
use App\Models\Habit;
use App\Models\HabitCompletion;
use App\Models\Stat;
use App\Models\User;
use App\Observers\HabitCompletionObserver;
use App\Services\StatService;

test('creating completion triggers stat recalculation', function (): void {
    $user = User::factory()->create(['birthdate' => null]);
    $habit = Habit::factory()->for($user)->create(['is_active' => true, 'rrule' => 'FREQ=DAILY', 'iterations_required' => 1]);

    HabitCompletion::factory()->for($habit)->for($user)->create([
        'completed_at' => now(),
        'current_iteration' => 1,
    ]);

    expect(Stat::query()->where('user_id', $user->id)->where('period', StatPeriod::Daily)->exists())->toBeTrue()
        ->and(Stat::query()->where('user_id', $user->id)->where('period', StatPeriod::Weekly)->exists())->toBeTrue();
});

test('deleting completion triggers stat recalculation', function (): void {
    $user = User::factory()->create(['birthdate' => null]);
    $habit = Habit::factory()->for($user)->create(['is_active' => true, 'rrule' => 'FREQ=DAILY', 'iterations_required' => 1]);
    $completion = HabitCompletion::factory()->for($habit)->for($user)->create([
        'completed_at' => now(),
        'current_iteration' => 1,
    ]);

    $completion->delete();

    $dailyStat = Stat::query()->where('user_id', $user->id)->where('period', StatPeriod::Daily)->first();

    expect($dailyStat)->not->toBeNull()
        ->and($dailyStat->completed_count)->toBe(0);
});

test('updating completion triggers stat recalculation', function (): void {
    $user = User::factory()->create(['birthdate' => null]);
    $habit = Habit::factory()->for($user)->create(['is_active' => true, 'rrule' => 'FREQ=DAILY', 'iterations_required' => 1]);
    $completion = HabitCompletion::factory()->for($habit)->for($user)->create([
        'completed_at' => now(),
        'current_iteration' => 1,
    ]);

    $completion->update(['notes' => 'updated']);

    expect(Stat::query()->where('user_id', $user->id)->where('period', StatPeriod::Daily)->exists())->toBeTrue();
});

test('observer handles null user gracefully', function (): void {
    $completion = new HabitCompletion;
    $completion->completed_at = now()->toDateString();

    // Create a completion without a habit/user relationship
    // The observer should not throw when user is null
    $observer = new HabitCompletionObserver(resolve(StatService::class));

    $observer->created($completion);
    $observer->deleted($completion);

    expect(Stat::query()->count())->toBe(0);
});
