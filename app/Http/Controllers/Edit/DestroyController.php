<?php

declare(strict_types=1);

namespace App\Http\Controllers\Edit;

use App\Http\Requests\Edit\DestroyHabitRequest;
use App\Models\Habit;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Response;

/**
 * Delete a habit.
 */
#[Group('Edit', weight: 1)]
final readonly class DestroyController
{
    /**
     * Delete a habit.
     *
     * Soft-deletes the habit, or force-deletes if no completions exist.
     */
    public function destroy(DestroyHabitRequest $request, Habit $habit): Response
    {
        $habit->delete();

        return response()->noContent();
    }
}
