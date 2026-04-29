<?php

declare(strict_types=1);

namespace App\Data\HabitActivity;

/**
 * A single day in the week grid header.
 */
final readonly class WeekDay
{
    public function __construct(
        public string $date,
        public string $dayName,
        public int $dayNumber,
        public bool $isToday,
        public bool $isFuture,
    ) {}
}
