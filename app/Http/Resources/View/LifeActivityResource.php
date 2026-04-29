<?php

declare(strict_types=1);

namespace App\Http\Resources\View;

use App\Data\LifeGrid\YearlyActivity;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Yearly activity data point for the life heatmap.
 *
 * @property-read YearlyActivity $resource
 */
final class LifeActivityResource extends JsonResource
{
    /**
     * @return array{year: int, intensity: int, completed: int, total: int}
     */
    public function toArray(Request $request): array
    {
        return [
            /**
             * Life year index (age, 0-based).
             *
             * @var int
             *
             * @example 24
             */
            'year' => $this->resource->year,

            /**
             * Heatmap intensity level (0–4).
             *
             * @var int
             *
             * @example 3
             */
            'intensity' => $this->resource->intensity,

            /**
             * Number of habits completed that year.
             *
             * @var int
             *
             * @example 120
             */
            'completed' => $this->resource->completed,

            /**
             * Total habit occurrences scheduled that year.
             *
             * @var int
             *
             * @example 200
             */
            'total' => $this->resource->total,
        ];
    }
}
