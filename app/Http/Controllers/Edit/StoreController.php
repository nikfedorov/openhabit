<?php

declare(strict_types=1);

namespace App\Http\Controllers\Edit;

use App\Actions\Edit\GetUserHabitsAction;
use App\Actions\Edit\SaveHabitAction;
use App\Http\Requests\Edit\StoreHabitRequest;
use App\Http\Resources\Edit\EditHabitResource;
use App\Models\Habit;
use App\Models\User;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Create or update a habit.
 */
#[Group('Edit', weight: 1)]
final readonly class StoreController
{
    public function __construct(
        private SaveHabitAction $saveHabit,
        private GetUserHabitsAction $getUserHabits,
    ) {}

    /**
     * Create a new habit.
     */
    public function store(StoreHabitRequest $request, #[CurrentUser] User $user): AnonymousResourceCollection
    {
        $this->saveHabit->handle($user, $request->habitData());

        return EditHabitResource::collection($this->getUserHabits->handle($user));
    }

    /**
     * Update an existing habit.
     */
    public function update(StoreHabitRequest $request, Habit $habit, #[CurrentUser] User $user): EditHabitResource
    {
        $habit = $this->saveHabit->handle($user, $request->habitData(), $habit);
        $habit->load('category');

        return new EditHabitResource($habit);
    }
}
