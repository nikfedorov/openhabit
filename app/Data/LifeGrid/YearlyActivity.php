<?php

declare(strict_types=1);

namespace App\Data\LifeGrid;

/**
 * Yearly activity data point for the life heatmap.
 */
final readonly class YearlyActivity
{
    public function __construct(
        public int $year,
        public int $intensity,
        public int $completed,
        public int $total,
    ) {}
}
