<?php

declare(strict_types=1);

namespace App\Http\Resources\Edit;

use App\Models\Habit;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A habit in the edit list.
 *
 * @mixin Habit
 */
final class EditHabitResource extends JsonResource
{
    /**
     * @return array{id: int, name: string, description: string|null, is_active: bool, sort_order: int, iterations_required: int, human_readable: string, is_franklin_virtue: bool}
     */
    public function toArray(Request $request): array
    {
        return [
            /**
             * Unique habit identifier.
             *
             * @var int
             *
             * @example 1
             */
            'id' => $this->id,

            /**
             * Display name of the habit.
             *
             * @var string
             *
             * @example "Morning meditation"
             */
            'name' => $this->name,

            /**
             * Optional longer description.
             *
             * @var string|null
             *
             * @example "10 minutes before breakfast"
             */
            'description' => $this->description,

            /**
             * Whether the habit is currently active and shown in the tracker.
             *
             * @var bool
             */
            'is_active' => $this->is_active,

            /**
             * Position in the sorted habit list.
             *
             * @var int
             *
             * @example 3
             */
            'sort_order' => $this->sort_order,

            /**
             * How many completions are required per day.
             *
             * @var int
             *
             * @example 1
             */
            'iterations_required' => $this->iterations_required,

            /**
             * Human-readable schedule description derived from the RRule.
             *
             * @var string
             *
             * @example "Monday, Wednesday, Friday"
             */
            'human_readable' => $this->human_readable,

            /**
             * Whether this habit belongs to the Franklin's Virtues category.
             *
             * @var bool
             */
            'is_franklin_virtue' => $this->is_franklin_virtue,
        ];
    }
}
