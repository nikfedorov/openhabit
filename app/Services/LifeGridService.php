<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\LifeGrid\LifeStats;
use App\Data\LifeGrid\WeeklyActivity;
use App\Data\LifeGrid\YearlyActivity;
use App\Models\Stat;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;

/**
 * Service for calculating life grid visualization data.
 *
 * Provides data for both life grid (years) and year grid (weeks)
 * visualizations based on user's birthdate and activity stats.
 */
final readonly class LifeGridService
{
    public const int TOTAL_LIFE_YEARS = 80;

    private const int WEEKS_PER_YEAR = 52;

    public function __construct(
        private LifeYearCalculator $lifeYearCalculator,
    ) {}

    /**
     * Get weekly activity data for a specific life year.
     *
     * @return array<int, WeeklyActivity>
     */
    public function getWeeklyActivityData(User $user, CarbonInterface $birthdate, int $selectedYear): array
    {
        $yearStart = $this->lifeYearCalculator->getYearStart($birthdate, $selectedYear);
        $yearEnd = $this->lifeYearCalculator->getYearStart($birthdate, $selectedYear + 1);

        $yearStats = $user->stats()
            ->select(['user_id', 'period_start', 'completed_count', 'planned_count'])
            ->weekly()
            ->where('period_start', '>=', $yearStart)
            ->where('period_start', '<', $yearEnd)
            ->get()
            ->keyBy(fn (Stat $stat): string => Date::parse($stat->period_start)->toDateString());

        return $this->buildWeeklyActivityArray($yearStats, $yearStart);
    }

    /**
     * Get all life-related data in a single pass (one DB query).
     *
     * @return array{lifeStats: LifeStats|null, weeklyActivityData: array<int, WeeklyActivity>|null, yearlyActivityData: array<int, YearlyActivity>|null}
     */
    public function getLifeData(User $user, ?CarbonInterface $birthdate, ?int $selectedYear): array
    {
        if (! $birthdate instanceof CarbonInterface) {
            return [
                'lifeStats' => null,
                'weeklyActivityData' => null,
                'yearlyActivityData' => null,
            ];
        }

        $allWeeklyStats = $user->stats()
            ->select(['user_id', 'period_start', 'completed_count', 'planned_count'])
            ->weekly()
            ->get();

        $weeklyActivityData = null;
        if ($selectedYear !== null) {
            $yearStart = $this->lifeYearCalculator->getYearStart($birthdate, $selectedYear);
            $yearEnd = $this->lifeYearCalculator->getYearStart($birthdate, $selectedYear + 1);

            $yearStats = $allWeeklyStats
                ->filter(fn (Stat $stat): bool => $stat->period_start >= $yearStart && $stat->period_start < $yearEnd)
                ->keyBy(fn (Stat $stat): string => Date::parse($stat->period_start)->toDateString());

            $weeklyActivityData = $this->buildWeeklyActivityArray($yearStats, $yearStart);
        }

        return [
            'lifeStats' => $this->getLifeStats($birthdate),
            'weeklyActivityData' => $weeklyActivityData,
            'yearlyActivityData' => $this->buildYearlyActivityArray($allWeeklyStats, $birthdate),
        ];
    }

    /**
     * Calculate current life stats from birthdate.
     */
    public function getLifeStats(CarbonInterface $birthdate): LifeStats
    {
        $currentAge = $this->lifeYearCalculator->getCurrentAge($birthdate);
        $weeksLived = $this->lifeYearCalculator->getWeeksLived($birthdate);

        return new LifeStats(
            currentAge: $currentAge,
            weeksLived: $weeksLived,
            yearsRemaining: self::TOTAL_LIFE_YEARS - $currentAge,
        );
    }

    /**
     * Build weekly activity array for a life year.
     *
     * @param  Collection<string, Stat>  $stats
     * @return array<int, WeeklyActivity>
     */
    private function buildWeeklyActivityArray(Collection $stats, CarbonInterface $yearStart): array
    {
        $weeklyData = [];
        $currentDate = $yearStart->copy();

        for ($weekNum = 0; $weekNum < self::WEEKS_PER_YEAR; $weekNum++) {
            $weekKey = $currentDate->toDateString();
            $weekStat = $stats->get($weekKey);

            $completed = $weekStat instanceof Stat ? $weekStat->completed_count : 0;
            $total = $weekStat instanceof Stat ? $weekStat->planned_count : 0;

            $weeklyData[$weekNum] = new WeeklyActivity(
                weekNum: $weekNum,
                intensity: Stat::calculateIntensity($completed, $total),
                completed: $completed,
                total: $total,
            );

            $currentDate = $currentDate->addWeek();
        }

        return $weeklyData;
    }

    /**
     * Build yearly activity array for life grid.
     *
     * @param  Collection<int, Stat>  $weeklyStats
     * @return array<int, YearlyActivity>
     */
    private function buildYearlyActivityArray(Collection $weeklyStats, CarbonInterface $birthdate): array
    {
        $yearlyData = [];

        for ($year = 0; $year < self::TOTAL_LIFE_YEARS; $year++) {
            $yearStart = $this->lifeYearCalculator->getYearStart($birthdate, $year);
            $yearEnd = $this->lifeYearCalculator->getYearStart($birthdate, $year + 1);

            $yearWeeklyStats = $weeklyStats->filter(
                fn (Stat $stat): bool => $stat->period_start >= $yearStart && $stat->period_start < $yearEnd
            );

            /** @var int $completed */
            $completed = $yearWeeklyStats->sum('completed_count');
            /** @var int $total */
            $total = $yearWeeklyStats->sum('planned_count');

            $yearlyData[$year] = new YearlyActivity(
                year: $year,
                intensity: Stat::calculateIntensity($completed, $total),
                completed: $completed,
                total: $total,
            );
        }

        return $yearlyData;
    }
}
