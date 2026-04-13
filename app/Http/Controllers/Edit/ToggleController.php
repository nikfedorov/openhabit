<?php

declare(strict_types=1);

namespace App\Http\Controllers\Edit;

use App\Actions\Edit\ToggleHabitAction;
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
    public function __construct(private ToggleHabitAction $toggleHabitAction) {}

    /**
     * Toggle habit active/inactive.
     */
    public function store(ToggleHabitRequest $request, Habit $habit): Response
    {
        $this->toggleHabitAction->handle($habit);

        return response()->noContent();
    }
}
