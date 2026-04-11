<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Habit;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A habit assigned to the user.
 *
 * @mixin Habit
 */
final class HabitResource extends JsonResource
{
    /**
     * @return array{id: int, name: string, description: string|null, iterations_required: int, is_completed: bool, current_iteration: int, sort_order: int}
     */
    public function toArray(Request $request): array
    {
        /** @var Habit $habit */
        $habit = $this->resource;
        /** @var int $currentIteration */
        $currentIteration = $habit->getAttribute('current_iteration_for_date') ?? 0;

        return [
            /**
             * Unique habit identifier.
             */
            'id' => $this->id,

            /**
             * Display name of the habit.
             *
             * @example "Morning exercise"
             */
            'name' => $this->name,

            /**
             * Optional longer description.
             *
             * @example "Do 30 pushups"
             */
            'description' => $this->description,

            /**
             * How many iterations are needed to complete.
             *
             * @example 3
             */
            'iterations_required' => $this->iterations_required,

            /**
             * Whether the habit is fully completed for the day.
             */
            'is_completed' => $currentIteration >= $this->iterations_required,

            /**
             * Number of iterations completed so far today.
             *
             * @example 1
             */
            'current_iteration' => $currentIteration,

            /**
             * Position in the habit list.
             *
             * @example 2
             */
            'sort_order' => $this->sort_order,
        ];
    }
}
