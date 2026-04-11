<?php

declare(strict_types=1);

namespace App\Data\LifeGrid;

/**
 * Life statistics calculated from birthdate.
 */
final readonly class LifeStats
{
    public function __construct(
        public int $currentAge,
        public int $weeksLived,
        public int $yearsRemaining,
    ) {}
}
