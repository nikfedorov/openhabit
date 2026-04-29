<?php

declare(strict_types=1);

namespace App\Data\RRule;

/**
 * Parsed ordinal weekday from BYDAY rule (e.g. "1MO" → first Monday).
 */
final readonly class OrdinalWeekday
{
    public function __construct(
        public ?int $ordinal,
        public ?int $day,
    ) {}
}
