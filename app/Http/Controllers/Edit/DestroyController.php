<?php

declare(strict_types=1);

namespace App\Http\Controllers\Edit;

use App\Actions\Edit\DeleteHabitAction;
use App\Http\Requests\Edit\DestroyHabitRequest;
use App\Models\Habit;
use App\Models\User;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\Response;

/**
 * Delete a habit.
 */
#[Group('Edit', weight: 1)]
final readonly class DestroyController
{
    public function __construct(
        private DeleteHabitAction $deleteHabitAction,
    ) {}

    /**
     * Delete a habit.
     *
     * Soft-deletes the habit, or force-deletes if no completions exist.
     * Reorders remaining habits to close the sort_order gap.
     */
    public function destroy(DestroyHabitRequest $request, Habit $habit, #[CurrentUser] User $user): Response
    {
        $this->deleteHabitAction->handle($user, $habit);

        return response()->noContent();
    }
}
