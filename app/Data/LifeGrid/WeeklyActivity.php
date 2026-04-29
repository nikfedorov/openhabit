<?php

declare(strict_types=1);

namespace App\Data\LifeGrid;

/**
 * Weekly activity data point for the year heatmap.
 */
final readonly class WeeklyActivity
{
    public function __construct(
        public int $weekNum,
        public int $intensity,
        public int $completed,
        public int $total,
    ) {}
}
