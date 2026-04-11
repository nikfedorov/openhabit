<?php

declare(strict_types=1);

namespace App\Data\View;

use App\Data\HabitActivity\WeekDay;
use App\Data\HabitActivity\WeekGridHabit;

/**
 * Data returned by WeekAction.
 */
final readonly class WeekData
{
    /**
     * @param  array<int, WeekDay>  $days
     * @param  array<int, WeekGridHabit>  $habits
     */
    public function __construct(
        public string $start,
        public string $end,
        public string $startFormatted,
        public string $endFormatted,
        public string $endFormattedFull,
        public string $year,
        public bool $isCurrent,
        public array $days,
        public array $habits,
    ) {}
}
