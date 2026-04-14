<?php

declare(strict_types=1);

namespace App\Http\Controllers\Edit;

use App\Http\Requests\Edit\ToggleHabitRequest;
use App\Models\Habit;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Response;

/**
 * Toggle a habit's active status.
 */
#[Group('Edit', weight: 1)]
final readonly class ToggleController
{
    /**
     * Toggle habit active/inactive.
     */
    public function store(ToggleHabitRequest $request, Habit $habit): Response
    {
        $habit->update(['is_active' => ! $habit->is_active]);

        return response()->noContent();
    }
}
