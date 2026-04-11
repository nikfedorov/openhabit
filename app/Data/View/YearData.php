<?php

declare(strict_types=1);

namespace App\Data\View;

use App\Data\LifeGrid\WeeklyActivity;

/**
 * Data returned by YearAction.
 */
final readonly class YearData
{
    /**
     * @param  array<int, WeeklyActivity>|null  $activityData
     */
    public function __construct(
        public ?int $selected,
        public ?string $birthdate,
        public ?int $currentAge,
        public ?array $activityData,
    ) {}
}
