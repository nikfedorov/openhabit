<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\StatPeriod;
use App\Models\Habit;
use App\Models\HabitCompletion;
use App\Models\Stat;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * Service for recalculating habit statistics.
 */
final readonly class StatService
{
    public function __construct(
        private RRuleService $rruleService,
        private LifeYearCalculator $lifeYearCalculator,
    ) {}

    /**
     * Recalculate both daily and weekly stats for a user on a given date.
     * Loads habits once and reuses for both calculations.
     */
    public function recalculateForDate(User $user, CarbonInterface $date): void
    {
        $habits = $this->getActiveHabitsWithRRule($user);

        $this->recalculateDailyStat($user, $date, $habits);
        $this->recalculateWeeklyStat($user, $date, $habits);

        if ($user->birthdate instanceof CarbonInterface) {
            $this->recalculateYearlyStat($user, $date, $user->birthdate);
        }
    }

    /**
     * Recalculate daily stat for a user on a given date.
     *
     * @param  Collection<int, Habit>|null  $habits  Pre-loaded habits to avoid duplicate queries
     */
    public function recalculateDailyStat(User $user, CarbonInterface $date, ?Collection $habits = null): Stat
    {
        $habits ??= $this->getActiveHabitsWithRRule($user);
        $plannedCount = $this->countPlannedHabitsForDateWithHabits($habits, $date);
        $completedCount = $this->countCompletedHabitsForDate($user, $date);

        $stat = Stat::query()
            ->where('user_id', $user->id)
            ->where('period', StatPeriod::Daily)
            ->whereDate('period_start', $date)
            ->first();

        if ($stat === null) {
            $stat = new Stat;
            $stat->user_id = $user->id;
            $stat->period = StatPeriod::Daily;
            $stat->period_start = $date->toDateString();
        }

        $stat->planned_count = $plannedCount;
        $stat->completed_count = $completedCount;
        $stat->save();

        return $stat;
    }

    /**
     * Recalculate weekly stat for a user for the week containing the given date.
     *
     * @param  Collection<int, Habit>|null  $habits  Pre-loaded habits to avoid duplicate queries
     */
    public function recalculateWeeklyStat(User $user, CarbonInterface $date, ?Collection $habits = null): Stat
    {
        $weekStart = $date->copy()->startOfWeek();
        $weekEnd = $date->copy()->endOfWeek();

        $habits ??= $this->getActiveHabitsWithRRule($user);
        $plannedCount = $this->countPlannedHabitsForRangeWithHabits($habits, $weekStart, $weekEnd);
        $completedCount = $this->countCompletedHabitsForRange($user, $weekStart, $weekEnd);

        $stat = Stat::query()
            ->where('user_id', $user->id)
            ->where('period', StatPeriod::Weekly)
            ->whereDate('period_start', $weekStart)
            ->first();

        if ($stat === null) {
            $stat = new Stat;
            $stat->user_id = $user->id;
            $stat->period = StatPeriod::Weekly;
            $stat->period_start = $weekStart->toDateString();
        }

        $stat->planned_count = $plannedCount;
        $stat->completed_count = $completedCount;
        $stat->save();

        return $stat;
    }

    /**
     * Count how many habits are planned for a specific date based on rrule.
     */
    public function countPlannedHabitsForDate(User $user, CarbonInterface $date): int
    {
        return $this->countPlannedHabitsForDateWithHabits(
            $this->getActiveHabitsWithRRule($user),
            $date
        );
    }

    /**
     * Count how many habits were completed on a specific date.
     * Only counts habits that are fully completed (current_iteration >= iterations_required).
     */
    public function countCompletedHabitsForDate(User $user, CarbonInterface $date): int
    {
        return HabitCompletion::query()
            ->where('user_id', $user->id)
            ->whereDate('completed_at', $date)
            ->whereHas('habit', function (Builder $query): void {
                $query->whereColumn('habit_completions.current_iteration', '>=', 'habits.iterations_required');
            })
            ->count();
    }

    /**
     * Count how many habits are planned for a date range based on rrule.
     */
    public function countPlannedHabitsForRange(User $user, CarbonInterface $start, CarbonInterface $end): int
    {
        return $this->countPlannedHabitsForRangeWithHabits(
            $this->getActiveHabitsWithRRule($user),
            $start,
            $end
        );
    }

    /**
     * Count how many habits were completed in a date range.
     * Only counts habits that are fully completed (current_iteration >= iterations_required).
     */
    public function countCompletedHabitsForRange(User $user, CarbonInterface $start, CarbonInterface $end): int
    {
        return HabitCompletion::query()
            ->where('user_id', $user->id)
            ->whereDate('completed_at', '>=', $start)
            ->whereDate('completed_at', '<=', $end)
            ->whereHas('habit', function (Builder $query): void {
                $query->whereColumn('habit_completions.current_iteration', '>=', 'habits.iterations_required');
            })
            ->count();
    }

    /**
     * Recalculate yearly stat for a user for the life year containing the given date.
     * A life year starts on the Monday on or after the birthday.
     */
    public function recalculateYearlyStat(User $user, CarbonInterface $date, CarbonInterface $birthdate): Stat
    {
        $lifeYear = $this->lifeYearCalculator->getYearForDate($date, $birthdate);
        $yearStart = $this->lifeYearCalculator->getYearStart($birthdate, $lifeYear);
        $yearEnd = $this->lifeYearCalculator->getYearStart($birthdate, $lifeYear + 1)->subDay();

        /** @var object{completed: int|numeric-string|null, total: int|numeric-string|null}|null $weeklyStats */
        $weeklyStats = $user->stats()
            ->weekly()
            ->where('period_start', '>=', $yearStart)
            ->where('period_start', '<=', $yearEnd)
            ->selectRaw('SUM(completed_count) as completed, SUM(planned_count) as total')
            ->first();

        $completedCount = (int) ($weeklyStats->completed ?? 0);
        $plannedCount = (int) ($weeklyStats->total ?? 0);

        $stat = Stat::query()
            ->where('user_id', $user->id)
            ->where('period', StatPeriod::Yearly)
            ->whereDate('period_start', $yearStart)
            ->first();

        if ($stat === null) {
            $stat = new Stat;
            $stat->user_id = $user->id;
            $stat->period = StatPeriod::Yearly;
            $stat->period_start = $yearStart->toDateString();
        }

        $stat->planned_count = $plannedCount;
        $stat->completed_count = $completedCount;
        $stat->save();

        return $stat;
    }

    /**
     * Calculate intensity level (0-4) based on completion rate.
     */
    public function calculateIntensity(int $completed, int $total): int
    {
        if ($total === 0 || $completed === 0) {
            return 0;
        }

        $rate = $completed / $total;

        return match (true) {
            $rate >= 0.75 => 4,
            $rate >= 0.5 => 3,
            $rate >= 0.25 => 2,
            $rate > 0 => 1,
            default => 0,
        };
    }

    /**
     * Count how many habits are planned for a specific date using pre-loaded habits.
     *
     * @param  Collection<int, Habit>  $habits
     */
    private function countPlannedHabitsForDateWithHabits(Collection $habits, CarbonInterface $date): int
    {
        $count = 0;

        foreach ($habits as $habit) {
            /** @var string $rrule */
            $rrule = $habit->rrule;
            if ($this->rruleService->matchesDate($rrule, $date)) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Count how many habits are planned for a date range using pre-loaded habits.
     *
     * @param  Collection<int, Habit>  $habits
     */
    private function countPlannedHabitsForRangeWithHabits(Collection $habits, CarbonInterface $start, CarbonInterface $end): int
    {
        $count = 0;

        foreach ($habits as $habit) {
            /** @var string $rrule */
            $rrule = $habit->rrule;
            $occurrences = $this->rruleService->getOccurrencesBetween($rrule, $start, $end);
            $count += count($occurrences);
        }

        return $count;
    }

    /**
     * Get active habits with rrule for a user.
     *
     * @return Collection<int, Habit>
     */
    private function getActiveHabitsWithRRule(User $user): Collection
    {
        return Habit::query()
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->whereNotNull('rrule')
            ->get();
    }
}
