<?php

declare(strict_types=1);

namespace App\Http\Controllers\Edit;

use App\Actions\Edit\StoreAction;
use App\Http\Requests\Edit\StoreHabitRequest;
use App\Http\Resources\Edit\EditHabitResource;
use App\Models\Habit;
use App\Models\User;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Container\Attributes\CurrentUser;

/**
 * Create or update a habit.
 */
#[Group('Edit', weight: 1)]
final readonly class StoreController
{
    public function __construct(private StoreAction $storeAction) {}

    /**
     * Create a new habit.
     */
    public function store(StoreHabitRequest $request, #[CurrentUser] User $user): EditHabitResource
    {
        $habit = $this->storeAction->handle($user, $request->habitData());
        $habit->load('category');

        return new EditHabitResource($habit);
    }

    /**
     * Update an existing habit.
     */
    public function update(StoreHabitRequest $request, Habit $habit, #[CurrentUser] User $user): EditHabitResource
    {
        $habit = $this->storeAction->handle($user, $request->habitData(), $habit);
        $habit->load('category');

        return new EditHabitResource($habit);
    }
}
