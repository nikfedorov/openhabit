<?php

declare(strict_types=1);

namespace App\Http\Controllers\Edit;

use App\Actions\Edit\ToggleFranklinHabitsAction;
use App\Models\User;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\Response;

/**
 * Toggle the active status of all Franklin's Virtues habits at once.
 *
 * If any Franklin habit is currently active, all are deactivated.
 * If none are active, all are activated.
 */
#[Group('Edit', weight: 1)]
final readonly class ToggleFranklinController
{
    public function __construct(private ToggleFranklinHabitsAction $toggleFranklinHabits) {}

    /**
     * Toggle all Franklin virtue habits active/inactive for the authenticated user.
     */
    public function store(#[CurrentUser] User $user): Response
    {
        $this->toggleFranklinHabits->handle($user);

        return response()->noContent();
    }
}
