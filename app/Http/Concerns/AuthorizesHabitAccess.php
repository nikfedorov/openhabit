<?php

declare(strict_types=1);

namespace App\Http\Concerns;

use App\Models\Habit;

/**
 * Shared authorization for Edit endpoints that operate on a single habit.
 *
 * Verifies the authenticated user owns the route-bound habit
 * and, optionally, that the habit is not a Franklin virtue.
 *
 * Only use on routes with {habit} route model binding.
 */
trait AuthorizesHabitAccess
{
    /**
     * Authorize that the current user owns the route-bound habit.
     *
     * @param  bool  $allowFranklinVirtues  Whether Franklin virtue habits are allowed.
     */
    protected function authorizeHabitAccess(bool $allowFranklinVirtues = false): bool
    {
        /** @var Habit $habit */
        $habit = $this->route('habit');

        return $this->user()?->id === $habit->user_id
            && ($allowFranklinVirtues || ! $habit->is_franklin_virtue);
    }
}
