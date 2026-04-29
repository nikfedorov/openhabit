<?php

declare(strict_types=1);

namespace App\Actions\Edit;

use App\Models\User;

/**
 * Toggle all Franklin's Virtues habits for a user at once.
 *
 * If any Franklin habit is currently active, all are deactivated.
 * If none are active, all are activated.
 */
final readonly class ToggleFranklinHabitsAction
{
    /**
     * Toggle all Franklin virtue habits active/inactive for the given user.
     */
    public function handle(User $user): void
    {
        /** @var bool $anyActive */
        $anyActive = $user->habits()
            ->franklinVirtues()
            ->where('is_active', true)
            ->exists();

        $user->habits()
            ->franklinVirtues()
            ->update(['is_active' => ! $anyActive]);
    }
}
