<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read array<string, mixed> $resource
 */
final class ViewResource extends JsonResource
{
    /** @var string|null */
    public static $wrap;

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'tab' => $this->resource['tab'],
            'weekStart' => $this->resource['weekStart'],
            'weekEnd' => $this->resource['weekEnd'],
            'weekStartFormatted' => $this->resource['weekStartFormatted'],
            'weekEndFormatted' => $this->resource['weekEndFormatted'],
            'weekEndFormattedFull' => $this->resource['weekEndFormattedFull'],
            'weekYear' => $this->resource['weekYear'],
            'isCurrentWeek' => $this->resource['isCurrentWeek'],
            'franklinGrid' => $this->resource['franklinGrid'],
            'selectedYear' => $this->resource['selectedYear'],
            'birthdate' => $this->resource['birthdate'],
            'currentAge' => $this->resource['currentAge'],
            'lifeStats' => $this->resource['lifeStats'],
            'weeklyActivityData' => $this->resource['weeklyActivityData'],
            'yearlyActivityData' => $this->resource['yearlyActivityData'],
            'translations' => $this->resource['translations'],
            'navigationTranslations' => $this->resource['navigationTranslations'],
        ];
    }
}
