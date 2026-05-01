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

it('returns correct structure and separates franklin virtues', function (): void {
    $category = Category::factory()->create([
        'user_id' => $this->user->id,
        'slug' => Category::FRANKLIN_VIRTUES_SLUG,
    ]);
    Habit::factory()->daily()->create(['user_id' => $this->user->id]);
    Habit::factory()->daily()->create(['user_id' => $this->user->id, 'category_id' => $category->id]);

    $result = $this->service->getFranklinGridData(loadHabits($this->user), now()->startOfWeek());

    expect($result->days)->toHaveCount(7)
        ->and($result->habits)->toHaveCount(2);

    $regular = collect($result->habits)->where('isFranklinVirtue', false);
    $franklin = collect($result->habits)->where('isFranklinVirtue', true);
    expect($regular)->toHaveCount(1)
        ->and($franklin)->toHaveCount(1);
});

it('tracks completion and partial status', function (): void {
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

    expect($result->habits[0]->days[$today])
        ->completed->toBeTrue()
        ->partial->toBeFalse()
        ->and($result->habits[1]->days[$today])
        ->completed->toBeFalse()
        ->partial->toBeTrue();
});

it('marks today and scheduled days correctly', function (): void {
    Habit::factory()->create([
        'user_id' => $this->user->id,
        'rrule' => 'FREQ=WEEKLY;BYDAY=MO',
    ]);

    $result = $this->service->getFranklinGridData(loadHabits($this->user), now()->startOfWeek());

    $todayDay = collect($result->days)->firstWhere('date', now()->toDateString());
    expect($todayDay->isToday)->toBeTrue()
        ->and($todayDay->isFuture)->toBeFalse();

    $monday = now()->startOfWeek()->toDateString();
    $tuesday = now()->startOfWeek()->addDay()->toDateString();
    expect($result->habits[0]->days[$monday]->scheduled)->toBeTrue()
        ->and($result->habits[0]->days[$tuesday]->scheduled)->toBeFalse();
});

it('returns empty arrays for empty habits', function (): void {
    $result = $this->service->getFranklinGridData(loadHabits($this->user));

    expect($result->habits)->toBeEmpty();
});

it('uses provided today to mark isToday and isFuture correctly', function (): void {
    Habit::factory()->daily()->for($this->user)->create();

    $weekStart = now()->startOfWeek();
    // Set "today" to Wednesday of the current week
    $wednesday = $weekStart->copy()->addDays(2);
    $thursday = $weekStart->copy()->addDays(3);

    $result = $this->service->getFranklinGridData(loadHabits($this->user), $weekStart, $wednesday);

    $wednesdayDay = collect($result->days)->firstWhere('date', $wednesday->toDateString());
    $thursdayDay = collect($result->days)->firstWhere('date', $thursday->toDateString());

    expect($wednesdayDay->isToday)->toBeTrue()
        ->and($wednesdayDay->isFuture)->toBeFalse()
        ->and($thursdayDay->isToday)->toBeFalse()
        ->and($thursdayDay->isFuture)->toBeTrue();
});
