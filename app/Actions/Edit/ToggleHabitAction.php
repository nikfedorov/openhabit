<?php

declare(strict_types=1);

namespace App\Actions\Edit;

use App\Models\Habit;

/**
 * Toggle a single habit's active status.
 */
final readonly class ToggleHabitAction
{
    /**
     * Toggle the habit's is_active flag.
     */
    public function handle(Habit $habit): void
    {
        $habit->update(['is_active' => ! $habit->is_active]);
    }
}
