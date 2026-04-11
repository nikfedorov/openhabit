<?php

declare(strict_types=1);

namespace App\Http\Resources\View;

use App\Data\View\YearData;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Year heatmap data for the View page.
 *
 * @property-read YearData $resource
 */
final class YearViewResource extends JsonResource
{
    /**
     * @return array{selected: int|null, birthdate: string|null, currentAge: int|null}
     */
    public function toArray(Request $request): array
    {
        return [
            /**
             * Selected year index (age) for the year grid.
             *
             * @var int|null
             *
             * @example 25
             */
            'selected' => $this->resource->selected,

            /**
             * User's birthdate (YYYY-MM-DD).
             *
             * @var string|null
             *
             * @example "2001-03-15"
             */
            'birthdate' => $this->resource->birthdate,

            /**
             * User's current age in years.
             *
             * @var int|null
             *
             * @example 25
             */
            'currentAge' => $this->resource->currentAge,
        ];
    }
}
