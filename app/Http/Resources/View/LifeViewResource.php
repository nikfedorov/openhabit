<?php

declare(strict_types=1);

namespace App\Http\Resources\View;

use App\Data\View\LifeData;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Life (memento mori) grid data for the View page.
 *
 * @property-read LifeData $resource
 */
final class LifeViewResource extends JsonResource
{
    /**
     * @return array{birthdate: string|null, currentAge: int|null, weeksLived: int|null, yearsRemaining: int|null}
     */
    public function toArray(Request $request): array
    {
        return [
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

            /**
             * Total weeks lived since birth.
             *
             * @var int|null
             *
             * @example 1300
             */
            'weeksLived' => $this->resource->weeksLived,

            /**
             * Estimated years remaining (based on 80-year lifespan).
             *
             * @var int|null
             *
             * @example 55
             */
            'yearsRemaining' => $this->resource->yearsRemaining,
        ];
    }
}
