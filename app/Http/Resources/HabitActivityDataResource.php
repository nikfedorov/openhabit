<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Daily activity data point for the heatmap.
 *
 * @property-read array{date: string, percentage: float, completed: int, total: int, intensity: int} $resource
 */
final class HabitActivityDataResource extends JsonResource
{
    /**
     * @return array{date: string, percentage: float, completed: int, total: int, intensity: int}
     */
    public function toArray(Request $request): array
    {
        return [
            /**
             * Date of the activity entry (YYYY-MM-DD).
             *
             * @var string
             *
             * @example "2026-04-06"
             */
            'date' => $this->resource['date'],

            /**
             * Completion percentage for the day (0.0–100.0).
             *
             * @var float
             *
             * @example 66.7
             */
            'percentage' => $this->resource['percentage'],

            /**
             * Number of habits completed on this day.
             *
             * @var int
             *
             * @example 4
             */
            'completed' => $this->resource['completed'],

            /**
             * Total number of habits planned for this day.
             *
             * @var int
             *
             * @example 6
             */
            'total' => $this->resource['total'],

            /**
             * Heatmap intensity level (0–4).
             *
             * @var int
             *
             * @example 3
             */
            'intensity' => $this->resource['intensity'],
        ];
    }
}
