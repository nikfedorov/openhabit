<?php

declare(strict_types=1);

namespace App\Services;

use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Date;

/**
 * Service for calculating life year boundaries based on birthdate.
 *
 * A "life year" starts on the Monday on or after each birthday,
 * aligning years with week boundaries for consistent week-based tracking.
 */
final readonly class LifeYearCalculator
{
    /**
     * Calculate the start of a life year (Monday on or after birthday).
     *
     * @param  CarbonInterface  $birthdate  The user's birthdate
     * @param  int  $year  The life year (0 = birth year, 1 = first year, etc.)
     */
    public function getYearStart(CarbonInterface $birthdate, int $year): CarbonInterface
    {
        $birthday = Date::parse($birthdate)->addYears($year);

        return $birthday->dayOfWeek === CarbonInterface::MONDAY
            ? $birthday
            : $birthday->next(CarbonInterface::MONDAY);
    }

    /**
     * Calculate which life year a given date falls into.
     *
     * @param  CarbonInterface  $date  The date to check
     * @param  CarbonInterface  $birthdate  The user's birthdate
     * @return int The life year (0-based)
     */
    public function getYearForDate(CarbonInterface $date, CarbonInterface $birthdate): int
    {
        $approximateAge = (int) $birthdate->diffInYears($date);

        for ($year = max(0, $approximateAge - 1); $year <= $approximateAge + 1 && $year < 100; $year++) {
            $yearStart = $this->getYearStart($birthdate, $year);
            $nextYearStart = $this->getYearStart($birthdate, $year + 1);

            if ($date >= $yearStart && $date < $nextYearStart) {
                return $year;
            }
        }

        return 0;
    }

    /**
     * Calculate the current age in whole years.
     */
    public function getCurrentAge(CarbonInterface $birthdate): int
    {
        return (int) $birthdate->diffInYears(now());
    }

    /**
     * Calculate the total weeks lived since birth.
     */
    public function getWeeksLived(CarbonInterface $birthdate): int
    {
        return (int) $birthdate->diffInWeeks(now());
    }
}
