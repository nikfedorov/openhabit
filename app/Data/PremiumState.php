<?php

declare(strict_types=1);

namespace App\Data;

/**
 * Resolved premium / trial state for a user.
 */
final readonly class PremiumState
{
    public function __construct(
        public bool $hasPremium,
        public bool $isTrialing,
        public bool $shouldShowBanner,
        public ?string $trialRemaining,
    ) {}
}
