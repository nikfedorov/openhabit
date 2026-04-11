<?php

declare(strict_types=1);

namespace App\Http\Resources\View;

use App\Data\LifeGrid\WeeklyActivity;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Weekly activity data point for the year heatmap.
 *
 * @property-read WeeklyActivity $resource
 */
final class YearActivityResource extends JsonResource
{
    /**
     * @return array{weekNum: int, intensity: int, completed: int, total: int}
     */
    public function toArray(Request $request): array
    {
        return [
            /**
             * Week number within the life year (1-based).
             *
             * @var int
             *
             * @example 12
             */
            'weekNum' => $this->resource->weekNum,

            /**
             * Heatmap intensity level (0–4).
             *
             * @var int
             *
             * @example 3
             */
            'intensity' => $this->resource->intensity,

            /**
             * Number of habits completed that week.
             *
             * @var int
             *
             * @example 5
             */
            'completed' => $this->resource->completed,

            /**
             * Total habit occurrences scheduled that week.
             *
             * @var int
             *
             * @example 7
             */
            'total' => $this->resource->total,
        ];
    }
}
