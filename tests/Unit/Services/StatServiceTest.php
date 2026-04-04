<?php

declare(strict_types=1);

use App\Enums\StatPeriod;
use App\Models\Habit;
use App\Models\HabitCompletion;
use App\Models\Stat;
use App\Models\User;
use App\Services\LifeYearCalculator;
use App\Services\RRuleService;
use App\Services\StatService;

beforeEach(function (): void {
    $this->service = new StatService(new RRuleService, new LifeYearCalculator);
});

test('recalculateDailyStat creates daily stat', function (): void {
    $user = User::factory()->create();
    Habit::factory()->for($user)->create(['is_active' => true, 'rrule' => 'FREQ=DAILY']);

    $stat = $this->service->recalculateDailyStat($user, now());

    expect($stat->period)->toBe(StatPeriod::Daily)
        ->and($stat->planned_count)->toBe(1)
        ->and($stat->completed_count)->toBe(0);
});

test('recalculateDailyStat updates existing stat', function (): void {
    $user = User::factory()->create();
    Stat::factory()->for($user)->create([
        'period' => StatPeriod::Daily,
        'period_start' => now(),
        'planned_count' => 0,
        'completed_count' => 0,
    ]);
    Habit::factory()->for($user)->create(['is_active' => true, 'rrule' => 'FREQ=DAILY']);

    $stat = $this->service->recalculateDailyStat($user, now());

    expect($stat->planned_count)->toBe(1);
    expect(Stat::query()->where('user_id', $user->id)->where('period', StatPeriod::Daily)->count())->toBe(1);
});

test('recalculateWeeklyStat creates weekly stat', function (): void {
    $user = User::factory()->create();
    Habit::factory()->for($user)->create(['is_active' => true, 'rrule' => 'FREQ=DAILY']);

    $stat = $this->service->recalculateWeeklyStat($user, now());

    expect($stat->period)->toBe(StatPeriod::Weekly)
        ->and($stat->planned_count)->toBeGreaterThan(0);
});

test('recalculateWeeklyStat updates existing stat', function (): void {
    $user = User::factory()->create();
    Stat::factory()->for($user)->create([
        'period' => StatPeriod::Weekly,
        'period_start' => now()->startOfWeek(),
        'planned_count' => 0,
    ]);
    Habit::factory()->for($user)->create(['is_active' => true, 'rrule' => 'FREQ=DAILY']);

    $stat = $this->service->recalculateWeeklyStat($user, now());

    expect($stat->planned_count)->toBeGreaterThan(0);
});

test('recalculateForDate recalculates daily and weekly', function (): void {
    $user = User::factory()->create(['birthdate' => null]);
    Habit::factory()->for($user)->create(['is_active' => true, 'rrule' => 'FREQ=DAILY']);

    $this->service->recalculateForDate($user, now());

    expect(Stat::query()->where('user_id', $user->id)->count())->toBe(2);
});

test('recalculateForDate includes yearly when user has birthdate', function (): void {
    $user = User::factory()->create(['birthdate' => now()->subYears(25)]);
    Habit::factory()->for($user)->create(['is_active' => true, 'rrule' => 'FREQ=DAILY']);

    $this->service->recalculateForDate($user, now());

    expect(Stat::query()->where('user_id', $user->id)->where('period', StatPeriod::Yearly)->count())->toBe(1);
});

test('countPlannedHabitsForDate counts matching habits', function (): void {
    $user = User::factory()->create();
    Habit::factory()->for($user)->create(['is_active' => true, 'rrule' => 'FREQ=DAILY']);
    Habit::factory()->for($user)->create(['is_active' => true, 'rrule' => null]);
    Habit::factory()->for($user)->create(['is_active' => false, 'rrule' => 'FREQ=DAILY']);

    expect($this->service->countPlannedHabitsForDate($user, now()))->toBe(1);
});

test('countCompletedHabitsForDate counts fully completed habits', function (): void {
    $user = User::factory()->create();
    $habit = Habit::factory()->for($user)->create(['iterations_required' => 1]);
    HabitCompletion::factory()->for($habit)->for($user)->create([
        'completed_at' => now(),
        'current_iteration' => 1,
    ]);

    expect($this->service->countCompletedHabitsForDate($user, now()))->toBe(1);
});

test('countCompletedHabitsForDate ignores partially completed', function (): void {
    $user = User::factory()->create();
    $habit = Habit::factory()->for($user)->create(['iterations_required' => 3]);
    HabitCompletion::factory()->for($habit)->for($user)->create([
        'completed_at' => now(),
        'current_iteration' => 1,
    ]);

    expect($this->service->countCompletedHabitsForDate($user, now()))->toBe(0);
});

test('countPlannedHabitsForRange counts across date range', function (): void {
    $user = User::factory()->create();
    Habit::factory()->for($user)->create(['is_active' => true, 'rrule' => 'FREQ=DAILY']);

    $start = now()->startOfWeek();
    $end = now()->endOfWeek();

    expect($this->service->countPlannedHabitsForRange($user, $start, $end))->toBe(7);
});

test('countCompletedHabitsForRange counts across date range', function (): void {
    $user = User::factory()->create();
    $habit = Habit::factory()->for($user)->create(['iterations_required' => 1]);
    HabitCompletion::factory()->for($habit)->for($user)->create([
        'completed_at' => now(),
        'current_iteration' => 1,
    ]);
    HabitCompletion::factory()->for($habit)->for($user)->create([
        'completed_at' => now()->subDay(),
        'current_iteration' => 1,
    ]);

    $count = $this->service->countCompletedHabitsForRange($user, now()->subWeek(), now());

    expect($count)->toBe(2);
});

test('recalculateYearlyStat creates yearly stat from weekly aggregation', function (): void {
    $birthdate = now()->subYears(25);
    $user = User::factory()->create(['birthdate' => $birthdate]);

    Stat::factory()->for($user)->create([
        'period' => StatPeriod::Weekly,
        'period_start' => now()->startOfWeek(),
        'planned_count' => 10,
        'completed_count' => 5,
    ]);

    $stat = $this->service->recalculateYearlyStat($user, now(), $birthdate);

    expect($stat->period)->toBe(StatPeriod::Yearly)
        ->and($stat->planned_count)->toBe(10)
        ->and($stat->completed_count)->toBe(5);
});

test('recalculateYearlyStat updates existing yearly stat', function (): void {
    $birthdate = now()->subYears(25);
    $user = User::factory()->create(['birthdate' => $birthdate]);
    $lifeYearCalc = new LifeYearCalculator;
    $lifeYear = $lifeYearCalc->getYearForDate(now(), $birthdate);
    $yearStart = $lifeYearCalc->getYearStart($birthdate, $lifeYear);

    Stat::factory()->for($user)->create([
        'period' => StatPeriod::Yearly,
        'period_start' => $yearStart,
        'planned_count' => 0,
        'completed_count' => 0,
    ]);

    $stat = $this->service->recalculateYearlyStat($user, now(), $birthdate);

    expect(Stat::query()->where('user_id', $user->id)->where('period', StatPeriod::Yearly)->count())->toBe(1);
});
