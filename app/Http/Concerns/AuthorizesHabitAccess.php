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
     * Franklin virtue habits are never accessible via single-habit endpoints.
     */
    protected function authorizeHabitAccess(): bool
    {
        /** @var Habit $habit */
        $habit = $this->route('habit');

        return $this->user()?->id === $habit->user_id
            && ! $habit->is_franklin_virtue;
    }
}
