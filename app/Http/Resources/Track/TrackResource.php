<?php

declare(strict_types=1);

namespace App\Http\Resources\Track;

use App\Data\Track\TrackData;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Daily habit tracking data for a specific date.
 *
 * @property-read TrackData $resource
 */
final class TrackResource extends JsonResource
{
    /**
     * @return array{date: string, dayName: string, dateFormatted: string, isToday: bool, totalHabits: int, completedCount: int, dailyNoteContent: string, translations: array<string, string>}
     */
    public function toArray(Request $request): array
    {
        return [
            /**
             * ISO 8601 date string (YYYY-MM-DD).
             *
             * @var string
             *
             * @example "2026-04-06"
             */
            'date' => $this->resource->date,

            /**
             * Localized day of week name.
             *
             * @var string
             *
             * @example "Monday"
             */
            'dayName' => $this->resource->dayName,

            /**
             * Localized full date.
             *
             * @var string
             *
             * @example "April 6, 2026"
             */
            'dateFormatted' => $this->resource->dateFormatted,

            /**
             * Whether this date is today.
             *
             * @var bool
             */
            'isToday' => $this->resource->isToday,

            /**
             * Total number of habits scheduled for this day.
             *
             * @var int
             *
             * @example 5
             */
            'totalHabits' => $this->resource->totalHabits,

            /**
             * Number of fully completed habits.
             *
             * @var int
             *
             * @example 3
             */
            'completedCount' => $this->resource->completedCount,

            /**
             * User's daily note text.
             *
             * @var string
             *
             * @example "Great day!"
             */
            'dailyNoteContent' => $this->resource->dailyNoteContent,

            /**
             * Localized UI strings for the track page.
             *
             * @var array<string, string>
             */
            'translations' => $this->resource->translations,
        ];
    }
}
