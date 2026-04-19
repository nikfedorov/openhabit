<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use App\Models\User;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\Response;

/**
 * Dismiss the trial banner for the authenticated user.
 */
#[Group('Settings', weight: 2)]
final readonly class DismissTrialBannerController
{
    /**
     * Record that the user has dismissed the trial banner.
     */
    public function store(#[CurrentUser] User $user): Response
    {
        $user->update(['trial_banner_dismissed_at' => now()]);

        return response()->noContent();
    }
}
