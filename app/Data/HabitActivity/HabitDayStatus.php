<?php

declare(strict_types=1);

namespace App\Data\HabitActivity;

/**
 * Completion status for a single day of a habit.
 */
final readonly class HabitDayStatus
{
    public function __construct(
        public bool $completed,
        public bool $partial,
        public bool $scheduled,
    ) {}
}
