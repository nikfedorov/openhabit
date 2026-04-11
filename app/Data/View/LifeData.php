<?php

declare(strict_types=1);

namespace App\Data\View;

use App\Data\LifeGrid\YearlyActivity;

/**
 * Data returned by LifeAction.
 */
final readonly class LifeData
{
    /**
     * @param  array<int, YearlyActivity>|null  $activityData
     */
    public function __construct(
        public ?string $birthdate,
        public ?int $currentAge,
        public ?int $weeksLived,
        public ?int $yearsRemaining,
        public ?array $activityData,
    ) {}
}
