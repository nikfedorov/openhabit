<?php

declare(strict_types=1);

namespace App\Actions;

use App\Data\PremiumState;
use App\Models\Setting;
use App\Models\User;
use Carbon\CarbonInterface;

final readonly class ResolvePremiumStateAction
{
    /**
     * Resolve the current premium / trial state for the given user.
     */
    public function handle(User $user): PremiumState
    {
        $hasActiveSubscription = $user->subscription_expires_at?->isFuture() ?? false;
        $trialExpiresAt = $this->trialExpiresAt($user);
        $isWithinTrialPeriod = $trialExpiresAt instanceof CarbonInterface && $trialExpiresAt->isFuture();
        $isTrialing = ! $hasActiveSubscription && $isWithinTrialPeriod;

        return new PremiumState(
            hasPremium: $hasActiveSubscription || $isWithinTrialPeriod,
            isTrialing: $isTrialing,
            shouldShowBanner: $isTrialing && ! $this->wasDismissedWithinLastDay($user->trial_banner_dismissed_at),
            trialRemaining: $isTrialing ? $this->trialRemaining($trialExpiresAt) : null,
        );
    }

    private function wasDismissedWithinLastDay(?CarbonInterface $dismissedAt): bool
    {
        return $dismissedAt instanceof CarbonInterface
            && $dismissedAt->gt(now()->subDay());
    }

    private function trialExpiresAt(User $user): ?CarbonInterface
    {
        $trialDays = Setting::trialPeriodDays();

        if ($trialDays <= 0) {
            return null;
        }

        return $user->created_at->addDays($trialDays);
    }

    private function trialRemaining(CarbonInterface $trialExpiresAt): string
    {
        return $trialExpiresAt->diffForHumans(
            syntax: CarbonInterface::DIFF_ABSOLUTE,
            parts: 2,
        );
    }
}
