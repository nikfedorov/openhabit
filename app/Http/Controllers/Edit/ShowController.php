<?php

declare(strict_types=1);

namespace App\Http\Controllers\Edit;

use App\Http\Requests\Edit\ShowHabitRequest;
use App\Http\Resources\Edit\HabitFormResource;
use App\Models\Habit;
use Dedoc\Scramble\Attributes\Group;

/**
 * Get a single habit for the edit form.
 */
#[Group('Edit', weight: 1)]
final readonly class ShowController
{
    /**
     * Get habit form data.
     *
     * Returns the habit with parsed RRule data for the edit form.
     */
    public function show(ShowHabitRequest $request, Habit $habit): HabitFormResource
    {
        $habit->load('notifications');

        return new HabitFormResource($habit);
    }
}
