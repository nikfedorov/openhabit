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
     * Recalculate daily, weekly, and (if birthdate set) yearly stats for a user on a given date.
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

        return $this->upsertStat(
            $user,
            StatPeriod::Daily,
            $date,
            $this->countPlannedHabitsForDateWithHabits($habits, $date),
            $this->countCompletedHabits($user, $date),
        );
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

        return $this->upsertStat(
            $user,
            StatPeriod::Weekly,
            $weekStart,
            $this->countPlannedHabitsForRangeWithHabits($habits, $weekStart, $weekEnd),
            $this->countCompletedHabits($user, $weekStart, $weekEnd),
        );
    }

    /**
     * Recalculate yearly stat for a user for the life year containing the given date.
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

        return $this->upsertStat(
            $user,
            StatPeriod::Yearly,
            $yearStart,
            (int) ($weeklyStats->total ?? 0),
            (int) ($weeklyStats->completed ?? 0),
        );
    }

    /**
     * Count how many habits are planned for a specific date based on rrule.
     */
    public function countPlannedHabitsForDate(User $user, CarbonInterface $date): int
    {
        return $this->countPlannedHabitsForDateWithHabits(
            $this->getActiveHabitsWithRRule($user),
            $date,
        );
    }

    /**
     * Count how many habits were completed on a specific date.
     * Only counts habits that are fully completed (current_iteration >= iterations_required).
     */
    public function countCompletedHabitsForDate(User $user, CarbonInterface $date): int
    {
        return $this->countCompletedHabits($user, $date);
    }

    /**
     * Count how many habits are planned for a date range based on rrule.
     */
    public function countPlannedHabitsForRange(User $user, CarbonInterface $start, CarbonInterface $end): int
    {
        return $this->countPlannedHabitsForRangeWithHabits(
            $this->getActiveHabitsWithRRule($user),
            $start,
            $end,
        );
    }

    /**
     * Count how many habits were completed in a date range.
     * Only counts habits that are fully completed (current_iteration >= iterations_required).
     */
    public function countCompletedHabitsForRange(User $user, CarbonInterface $start, CarbonInterface $end): int
    {
        return $this->countCompletedHabits($user, $start, $end);
    }

    // ─── Private Helpers ────────────────────────────────────────

    /**
     * Find-or-create a Stat record and update its counts.
     */
    private function upsertStat(User $user, StatPeriod $period, CarbonInterface $periodStart, int $plannedCount, int $completedCount): Stat
    {
        $stat = Stat::query()
            ->where('user_id', $user->id)
            ->where('period', $period)
            ->whereDate('period_start', $periodStart)
            ->first();

        if ($stat === null) {
            $stat = new Stat;
            $stat->user_id = $user->id;
            $stat->period = $period;
            $stat->period_start = $periodStart->toDateString();
        }

        $stat->planned_count = $plannedCount;
        $stat->completed_count = $completedCount;
        $stat->save();

        return $stat;
    }

    /**
     * Count completed habits for a single date or date range.
     */
    private function countCompletedHabits(User $user, CarbonInterface $start, ?CarbonInterface $end = null): int
    {
        return HabitCompletion::query()
            ->where('user_id', $user->id)
            ->when(
                $end instanceof CarbonInterface,
                fn (Builder $q) => $q->whereDate('completed_at', '>=', $start)->whereDate('completed_at', '<=', $end),
                fn (Builder $q) => $q->whereDate('completed_at', $start),
            )
            ->whereHas('habit', function (Builder $query): void {
                $query->whereColumn('habit_completions.current_iteration', '>=', 'habits.iterations_required');
            })
            ->count();
    }

    /**
     * Count planned habits for a specific date using pre-loaded habits.
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
     * Count planned habits for a date range using pre-loaded habits.
     *
     * @param  Collection<int, Habit>  $habits
     */
    private function countPlannedHabitsForRangeWithHabits(Collection $habits, CarbonInterface $start, CarbonInterface $end): int
    {
        $count = 0;

        foreach ($habits as $habit) {
            /** @var string $rrule */
            $rrule = $habit->rrule;
            $count += count($this->rruleService->getOccurrencesBetween($rrule, $start, $end));
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
