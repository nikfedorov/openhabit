<?php

declare(strict_types=1);

namespace App\Http\Resources\View;

use App\Data\HabitActivity\HabitDayStatus;
use App\Data\HabitActivity\WeekGridHabit;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A habit row in the week grid with daily completion statuses.
 *
 * @property-read WeekGridHabit $resource
 */
final class WeekGridHabitResource extends JsonResource
{
    /**
     * @return array{id: int, name: string, category: string|null, is_weekly_focus: bool, is_franklin_virtue: bool, days: array<string, HabitDayStatus>}
     */
    public function toArray(Request $request): array
    {
        return [
            /**
             * Habit ID.
             *
             * @var int
             *
             * @example 1
             */
            'id' => $this->resource->id,

            /**
             * Habit name.
             *
             * @var string
             *
             * @example "Exercise"
             */
            'name' => $this->resource->name,

            /**
             * Category name, if any.
             *
             * @var string|null
             *
             * @example "Health"
             */
            'category' => $this->resource->category,

            /**
             * Whether this habit is scheduled every day of the week.
             *
             * @var bool
             */
            'is_weekly_focus' => $this->resource->isWeeklyFocus,

            /**
             * Whether this habit belongs to Franklin's Virtues category.
             *
             * @var bool
             */
            'is_franklin_virtue' => $this->resource->isFranklinVirtue,

            /**
             * Daily completion statuses.
             *
             * @var array<string, array{completed: bool, partial: bool, scheduled: bool}>
             */
            'days' => $this->resource->days,
        ];
    }
}
