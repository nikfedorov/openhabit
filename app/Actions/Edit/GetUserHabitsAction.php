<?php

declare(strict_types=1);

namespace App\Actions\Edit;

use App\Models\Habit;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

/**
 * Fetch all habits for a user on the edit page.
 */
final readonly class GetUserHabitsAction
{
    /**
     * Get all habits for the given user, with their category, ordered.
     *
     * @return Collection<int, Habit>
     */
    public function handle(User $user): Collection
    {
        return $user->habits()
            ->with('category')
            ->ordered()
            ->get();
    }
}
