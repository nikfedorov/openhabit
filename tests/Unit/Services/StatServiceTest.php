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

it('returns daily stat for recalculateDailyStat', function (): void {
    $user = User::factory()->create();
    Habit::factory()->for($user)->create(['is_active' => true, 'rrule' => 'FREQ=DAILY']);

    $stat = $this->service->recalculateDailyStat($user, now());

    expect($stat->period)->toBe(StatPeriod::Daily)
        ->and($stat->planned_count)->toBe(1)
        ->and($stat->completed_count)->toBe(0);
});

it('updates existing stat for recalculateDailyStat', function (): void {
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

it('creates weekly stat for recalculateWeeklyStat', function (): void {
    $user = User::factory()->create();
    Habit::factory()->for($user)->create(['is_active' => true, 'rrule' => 'FREQ=DAILY']);

    $stat = $this->service->recalculateWeeklyStat($user, now());

    expect($stat->period)->toBe(StatPeriod::Weekly)
        ->and($stat->planned_count)->toBeGreaterThan(0);
});

it('updates existing stat for recalculateWeeklyStat', function (): void {
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

it('recalculates daily and weekly stats for recalculateForDate', function (): void {
    $user = User::factory()->create(['birthdate' => null]);
    Habit::factory()->for($user)->create(['is_active' => true, 'rrule' => 'FREQ=DAILY']);

    $this->service->recalculateForDate($user, now());

    expect(Stat::query()->where('user_id', $user->id)->count())->toBe(2);
});

it('includes yearly stat for recalculateForDate when user has birthdate', function (): void {
    $user = User::factory()->create(['birthdate' => now()->subYears(25)]);
    Habit::factory()->for($user)->create(['is_active' => true, 'rrule' => 'FREQ=DAILY']);

    $this->service->recalculateForDate($user, now());

    expect(Stat::query()->where('user_id', $user->id)->where('period', StatPeriod::Yearly)->count())->toBe(1);
});

it('counts planned habits for date', function (): void {
    $user = User::factory()->create();
    Habit::factory()->for($user)->create(['is_active' => true, 'rrule' => 'FREQ=DAILY']);
    Habit::factory()->for($user)->create(['is_active' => true, 'rrule' => null]);
    Habit::factory()->for($user)->create(['is_active' => false, 'rrule' => 'FREQ=DAILY']);

    expect($this->service->countPlannedHabitsForDate($user, now()))->toBe(1);
});

it('counts completed habits for date', function (): void {
    $user = User::factory()->create();
    $habit = Habit::factory()->for($user)->create(['iterations_required' => 1]);
    HabitCompletion::factory()->for($habit)->for($user)->create([
        'completed_at' => now(),
        'current_iteration' => 1,
    ]);

    expect($this->service->countCompletedHabitsForDate($user, now()))->toBe(1);
});

it('ignores partially completed habits for countCompletedHabitsForDate', function (): void {
    $user = User::factory()->create();
    $habit = Habit::factory()->for($user)->create(['iterations_required' => 3]);
    HabitCompletion::factory()->for($habit)->for($user)->create([
        'completed_at' => now(),
        'current_iteration' => 1,
    ]);

    expect($this->service->countCompletedHabitsForDate($user, now()))->toBe(0);
});

it('counts planned habits for range', function (): void {
    $user = User::factory()->create();
    Habit::factory()->for($user)->create(['is_active' => true, 'rrule' => 'FREQ=DAILY']);

    $start = now()->startOfWeek();
    $end = now()->endOfWeek();

    expect($this->service->countPlannedHabitsForRange($user, $start, $end))->toBe(7);
});

it('counts completed habits for range', function (): void {
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

it('creates yearly stat from weekly aggregation for recalculateYearlyStat', function (): void {
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

it('updates existing yearly stat for recalculateYearlyStat', function (): void {
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
