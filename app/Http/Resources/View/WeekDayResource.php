<?php

declare(strict_types=1);

namespace App\Http\Resources\View;

use App\Data\HabitActivity\WeekDay;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A single day in the week grid header.
 *
 * @property-read WeekDay $resource
 */
final class WeekDayResource extends JsonResource
{
    /**
     * @return array{date: string, day_name: string, day_number: int, is_today: bool, is_future: bool}
     */
    public function toArray(Request $request): array
    {
        return [
            /**
             * Date in YYYY-MM-DD format.
             *
             * @var string
             *
             * @example "2026-04-06"
             */
            'date' => $this->resource->date,

            /**
             * Short localized day name.
             *
             * @var string
             *
             * @example "Mon"
             */
            'day_name' => $this->resource->dayName,

            /**
             * Day of month number.
             *
             * @var int
             *
             * @example 6
             */
            'day_number' => $this->resource->dayNumber,

            /**
             * Whether this day is today.
             *
             * @var bool
             */
            'is_today' => $this->resource->isToday,

            /**
             * Whether this day is in the future.
             *
             * @var bool
             */
            'is_future' => $this->resource->isFuture,
        ];
    }
}
