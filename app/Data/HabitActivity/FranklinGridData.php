<?php

declare(strict_types=1);

namespace App\Data\HabitActivity;

/**
 * Combined week grid data: days header and habit rows.
 */
final readonly class FranklinGridData
{
    /**
     * @param  array<int, WeekDay>  $days
     * @param  array<int, WeekGridHabit>  $habits
     */
    public function __construct(
        public array $days,
        public array $habits,
    ) {}
}
