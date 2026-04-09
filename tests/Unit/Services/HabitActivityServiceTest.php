<?php

declare(strict_types=1);

use App\Models\Category;
use App\Models\Habit;
use App\Models\HabitCompletion;
use App\Models\User;
use App\Services\HabitActivityService;
use Illuminate\Database\Eloquent\Collection;

beforeEach(function (): void {
    $this->service = resolve(HabitActivityService::class);
    $this->user = User::factory()->create();
});

/**
 * @return Collection<int, Habit>
 */
function loadHabits(User $user): Collection
{
    return $user->habits()->with('category')->ordered()->get();
}

test('getFranklinGridData returns correct structure and separates franklin virtues', function (): void {
    $category = Category::factory()->create([
        'user_id' => $this->user->id,
        'slug' => Category::FRANKLIN_VIRTUES_SLUG,
    ]);
    Habit::factory()->daily()->create(['user_id' => $this->user->id]);
    Habit::factory()->daily()->create(['user_id' => $this->user->id, 'category_id' => $category->id]);

    $result = $this->service->getFranklinGridData(loadHabits($this->user), now()->startOfWeek());

    expect($result)
        ->toHaveKeys(['week_start', 'week_end', 'days', 'regular_habits', 'franklin_habits'])
        ->and($result['days'])->toHaveCount(7)
        ->and($result['regular_habits'])->toHaveCount(1)
        ->and($result['franklin_habits'])->toHaveCount(1);
});

test('getFranklinGridData tracks completion and partial status', function (): void {
    $fullyCompleted = Habit::factory()->daily()->create([
        'user_id' => $this->user->id,
        'iterations_required' => 1,
    ]);
    $partiallyCompleted = Habit::factory()->daily()->create([
        'user_id' => $this->user->id,
        'iterations_required' => 3,
    ]);

    $today = now()->toDateString();
    HabitCompletion::factory()->create([
        'habit_id' => $fullyCompleted->id,
        'user_id' => $this->user->id,
        'completed_at' => $today,
        'current_iteration' => 1,
    ]);
    HabitCompletion::factory()->create([
        'habit_id' => $partiallyCompleted->id,
        'user_id' => $this->user->id,
        'completed_at' => $today,
        'current_iteration' => 1,
    ]);

    $result = $this->service->getFranklinGridData(loadHabits($this->user), now()->startOfWeek());

    expect($result['regular_habits'][0]['days'][$today])
        ->completed->toBeTrue()
        ->partial->toBeFalse()
        ->and($result['regular_habits'][1]['days'][$today])
        ->completed->toBeFalse()
        ->partial->toBeTrue();
});

test('getFranklinGridData marks today and scheduled days correctly', function (): void {
    Habit::factory()->create([
        'user_id' => $this->user->id,
        'rrule' => 'FREQ=WEEKLY;BYDAY=MO',
    ]);

    $result = $this->service->getFranklinGridData(loadHabits($this->user), now()->startOfWeek());

    $todayDay = collect($result['days'])->firstWhere('date', now()->toDateString());
    expect($todayDay['is_today'])->toBeTrue()
        ->and($todayDay['is_future'])->toBeFalse();

    $monday = now()->startOfWeek()->toDateString();
    $tuesday = now()->startOfWeek()->addDay()->toDateString();
    expect($result['regular_habits'][0]['days'][$monday]['scheduled'])->toBeTrue()
        ->and($result['regular_habits'][0]['days'][$tuesday]['scheduled'])->toBeFalse();
});

test('getFranklinGridData returns empty arrays for empty habits and defaults to current week', function (): void {
    $result = $this->service->getFranklinGridData(loadHabits($this->user));

    expect($result['regular_habits'])->toBeEmpty()
        ->and($result['franklin_habits'])->toBeEmpty()
        ->and($result['week_start'])->toBe(now()->startOfWeek()->toDateString());
});
