<?php

declare(strict_types=1);

namespace App\Http\Resources\View;

use App\Data\View\WeekData;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Week grid data for the View page.
 *
 * @property-read WeekData $resource
 */
final class WeekViewResource extends JsonResource
{
    /**
     * @return array{start: string, end: string, startFormatted: string, endFormatted: string, endFormattedFull: string, year: string, isCurrent: bool}
     */
    public function toArray(Request $request): array
    {
        return [
            /**
             * Week start date (YYYY-MM-DD).
             *
             * @var string
             *
             * @example "2026-04-06"
             */
            'start' => $this->resource->start,

            /**
             * Week end date (YYYY-MM-DD).
             *
             * @var string
             *
             * @example "2026-04-12"
             */
            'end' => $this->resource->end,

            /**
             * Localized short start date.
             *
             * @var string
             *
             * @example "Apr 6"
             */
            'startFormatted' => $this->resource->startFormatted,

            /**
             * Localized short end date.
             *
             * @var string
             *
             * @example "Apr 12"
             */
            'endFormatted' => $this->resource->endFormatted,

            /**
             * Localized full end date with year.
             *
             * @var string
             *
             * @example "Apr 12, 2026"
             */
            'endFormattedFull' => $this->resource->endFormattedFull,

            /**
             * Year of the displayed week.
             *
             * @var string
             *
             * @example "2026"
             */
            'year' => $this->resource->year,

            /**
             * Whether the displayed week is the current week.
             *
             * @var bool
             */
            'isCurrent' => $this->resource->isCurrent,
        ];
    }
}
