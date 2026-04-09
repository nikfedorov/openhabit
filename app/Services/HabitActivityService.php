<?php

declare(strict_types=1);

namespace App\Services;

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
     * Get Franklin-style weekly grid data for all habits.
     *
     * @param  EloquentCollection<int, Habit>  $habits
     * @return array{
     *   week_start: string,
     *   week_end: string,
     *   days: array<int, array{date: string, day_name: string, day_number: int, is_today: bool, is_future: bool}>,
     *   regular_habits: array<int, array{id: int, name: string, category: string|null, is_weekly_focus: bool, days: array<string, array{completed: bool, partial: bool, scheduled: bool}>}>,
     *   franklin_habits: array<int, array{id: int, name: string, category: string|null, is_weekly_focus: bool, days: array<string, array{completed: bool, partial: bool, scheduled: bool}>}>
     * }
     */
    public function getFranklinGridData(EloquentCollection $habits, ?CarbonInterface $weekStart = null): array
    {
        $weekStart = ($weekStart ?? now())->startOfWeek();
        $weekEnd = $weekStart->copy()->endOfWeek();

        $days = $this->buildWeekDays($weekStart);
        $completionsByHabit = $this->loadCompletionsForHabits($habits, $weekStart, $weekEnd);

        $regularHabits = [];
        $franklinHabits = [];

        foreach ($habits as $habit) {
            $habitCompletions = $completionsByHabit[$habit->id] ?? collect();
            $habitData = $this->buildHabitGridData($habit, $habitCompletions, $days);

            if ($habit->category?->slug === Category::FRANKLIN_VIRTUES_SLUG) {
                $franklinHabits[] = $habitData;
            } else {
                $regularHabits[] = $habitData;
            }
        }

        return [
            'week_start' => $weekStart->toDateString(),
            'week_end' => $weekEnd->toDateString(),
            'days' => $days,
            'regular_habits' => $regularHabits,
            'franklin_habits' => $franklinHabits,
        ];
    }

    /**
     * Build the week days array with metadata.
     *
     * @return array<int, array{date: string, day_name: string, day_number: int, is_today: bool, is_future: bool}>
     */
    private function buildWeekDays(CarbonInterface $weekStart): array
    {
        $today = today();
        $days = [];

        for ($i = 0; $i < 7; $i++) {
            $date = $weekStart->copy()->addDays($i)->startOfDay();
            $days[] = [
                'date' => $date->toDateString(),
                'day_name' => $date->isoFormat('dd'),
                'day_number' => $date->day,
                'is_today' => $date->isSameDay($today),
                'is_future' => $date->isAfter($today),
            ];
        }

        return $days;
    }

    /**
     * Build grid data for a single habit.
     *
     * @param  Collection<string, HabitCompletion>  $completions
     * @param  array<int, array{date: string, day_name: string, day_number: int, is_today: bool, is_future: bool}>  $days
     * @return array{id: int, name: string, category: string|null, is_weekly_focus: bool, days: array<string, array{completed: bool, partial: bool, scheduled: bool}>}
     */
    private function buildHabitGridData(Habit $habit, Collection $completions, array $days): array
    {
        $daysData = [];
        $isWeeklyFocus = true;

        foreach ($days as $day) {
            $date = Date::parse($day['date']);
            $completion = $completions->get($day['date']);
            $scheduled = $this->isScheduledForDate($habit, $date);
            $isWeeklyFocus = $isWeeklyFocus && $scheduled;

            $daysData[$day['date']] = [
                'completed' => $this->isCompleted($completion, $habit),
                'partial' => $this->isPartial($completion, $habit),
                'scheduled' => $scheduled,
            ];
        }

        return [
            'id' => $habit->id,
            'name' => $habit->name,
            'category' => $habit->category?->name,
            'is_weekly_focus' => $isWeeklyFocus,
            'days' => $daysData,
        ];
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
