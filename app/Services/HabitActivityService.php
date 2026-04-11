<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\HabitActivity\FranklinGridData;
use App\Data\HabitActivity\HabitDayStatus;
use App\Data\HabitActivity\WeekDay;
use App\Data\HabitActivity\WeekGridHabit;
use App\Models\Category;
use App\Models\Habit;
use App\Models\HabitCompletion;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;

/**
 * Service for building Franklin-style weekly grid data.
 *
 * Generates a weekly habit grid showing completion status
 * for each habit on each day, separated into regular habits
 * and Franklin's Virtues.
 */
final readonly class HabitActivityService
{
    public function __construct(
        private RRuleService $rruleService,
    ) {}

    /**
     * Get weekly grid data for all habits.
     *
     * @param  EloquentCollection<int, Habit>  $habits
     */
    public function getFranklinGridData(EloquentCollection $habits, ?CarbonInterface $weekStart = null): FranklinGridData
    {
        $weekStart = ($weekStart ?? now())->startOfWeek();
        $weekEnd = $weekStart->copy()->endOfWeek();

        $days = $this->buildWeekDays($weekStart);
        $completionsByHabit = $this->loadCompletionsForHabits($habits, $weekStart, $weekEnd);

        $allHabits = [];

        foreach ($habits as $habit) {
            $habitCompletions = $completionsByHabit[$habit->id] ?? collect();
            $allHabits[] = $this->buildHabitGridData($habit, $habitCompletions, $days);
        }

        return new FranklinGridData(
            days: $days,
            habits: $allHabits,
        );
    }

    /**
     * Build the week days array with metadata.
     *
     * @return array<int, WeekDay>
     */
    private function buildWeekDays(CarbonInterface $weekStart): array
    {
        $today = today();
        $days = [];

        for ($i = 0; $i < 7; $i++) {
            $date = $weekStart->copy()->addDays($i)->startOfDay();
            $days[] = new WeekDay(
                date: $date->toDateString(),
                dayName: $date->isoFormat('dd'),
                dayNumber: $date->day,
                isToday: $date->isSameDay($today),
                isFuture: $date->isAfter($today),
            );
        }

        return $days;
    }

    /**
     * Build grid data for a single habit.
     *
     * @param  Collection<string, HabitCompletion>  $completions
     * @param  array<int, WeekDay>  $days
     */
    private function buildHabitGridData(Habit $habit, Collection $completions, array $days): WeekGridHabit
    {
        $daysData = [];
        $isWeeklyFocus = true;

        foreach ($days as $day) {
            $date = Date::parse($day->date);
            $completion = $completions->get($day->date);
            $scheduled = $this->isScheduledForDate($habit, $date);
            $isWeeklyFocus = $isWeeklyFocus && $scheduled;

            $daysData[$day->date] = new HabitDayStatus(
                completed: $this->isCompleted($completion, $habit),
                partial: $this->isPartial($completion, $habit),
                scheduled: $scheduled,
            );
        }

        return new WeekGridHabit(
            id: $habit->id,
            name: $habit->name,
            category: $habit->category?->name,
            isWeeklyFocus: $isWeeklyFocus,
            isFranklinVirtue: $habit->category?->slug === Category::FRANKLIN_VIRTUES_SLUG,
            days: $daysData,
        );
    }

    /**
     * Load completions for multiple habits, grouped by habit_id and keyed by date.
     *
     * @param  EloquentCollection<int, Habit>  $habits
     * @return array<int, Collection<string, HabitCompletion>>
     */
    private function loadCompletionsForHabits(
        EloquentCollection $habits,
        CarbonInterface $weekStart,
        CarbonInterface $weekEnd,
    ): array {
        if ($habits->isEmpty()) {
            return [];
        }

        $habitIds = $habits->pluck('id')->all();

        $completions = HabitCompletion::query()
            ->select(['habit_id', 'current_iteration', 'completed_at'])
            ->whereIn('habit_id', $habitIds)
            ->whereBetween('completed_at', [$weekStart->startOfDay(), $weekEnd->endOfDay()])
            ->get();

        /** @var array<int, Collection<string, HabitCompletion>> $result */
        $result = [];

        foreach ($completions->groupBy('habit_id') as $habitId => $habitCompletions) {
            $result[(int) $habitId] = $habitCompletions->keyBy(
                fn (HabitCompletion $c): string => Date::parse($c->completed_at)->toDateString()
            );
        }

        return $result;
    }

    /**
     * Check if a habit is scheduled for a specific date.
     */
    private function isScheduledForDate(Habit $habit, CarbonInterface $date): bool
    {
        return blank($habit->rrule) || $this->rruleService->matchesDate($habit->rrule, $date);
    }

    /**
     * Check if a completion represents a fully completed habit.
     */
    private function isCompleted(?HabitCompletion $completion, Habit $habit): bool
    {
        return $completion instanceof HabitCompletion && $completion->current_iteration >= $habit->iterations_required;
    }

    /**
     * Check if a completion represents a partially completed habit.
     */
    private function isPartial(?HabitCompletion $completion, Habit $habit): bool
    {
        return $completion instanceof HabitCompletion
            && $completion->current_iteration > 0
            && $completion->current_iteration < $habit->iterations_required;
    }
}
