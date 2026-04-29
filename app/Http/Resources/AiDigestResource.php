<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\AiDigest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * AI Digest resource.
 *
 * @property-read AiDigest $resource
 */
final class AiDigestResource extends JsonResource
{
    /**
     * @return array{date: string, dateLabel: string, content: string|null}
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
            'date' => $this->resource->date->toDateString(),

            /**
             * Localized full date with day name.
             *
             * @var string
             *
             * @example "April 6, 2026, Sunday"
             */
            'dateLabel' => $this->resource->date->isoFormat('LL, dddd'),

            /**
             * AI-generated digest content.
             *
             * @var string|null
             */
            'content' => $this->resource->content,
        ];
    }
}
