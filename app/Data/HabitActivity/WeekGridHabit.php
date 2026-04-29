<?php

declare(strict_types=1);

namespace App\Data\HabitActivity;

/**
 * A habit row in the week grid with daily completion statuses.
 */
final readonly class WeekGridHabit
{
    /**
     * @param  array<string, HabitDayStatus>  $days
     */
    public function __construct(
        public int $id,
        public string $name,
        public ?string $category,
        public bool $isWeeklyFocus,
        public bool $isFranklinVirtue,
        public array $days,
    ) {}
}
