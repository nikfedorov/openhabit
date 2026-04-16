<?php

declare(strict_types=1);

namespace App\Actions\Edit;

use App\Models\Habit;
use App\Models\User;

/**
 * Delete a habit and reorder remaining habits to close the sort_order gap.
 */
final readonly class DeleteHabitAction
{
    public function __construct(private ReorderAction $reorderAction) {}

    /**
     * Delete the habit (soft or force) and reorder remaining habits.
     */
    public function handle(User $user, Habit $habit): void
    {
        $habit->delete();

        $remainingIds = $user->habits()
            ->excludingFranklinVirtues()
            ->ordered()
            ->pluck('id')
            ->map(fn (mixed $id): int => is_numeric($id) ? (int) $id : 0)
            ->all();

        $this->reorderAction->handle($user, $remainingIds);
    }
}
