<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Navigation translations shared across all pages.
 */
final class CommonResource extends JsonResource
{
    /**
     * @return array{navigationTranslations: array<string, string>}
     */
    public function toArray(Request $request): array
    {
        /** @var array<string, string> $navigationTranslations */
        $navigationTranslations = trans('navigation');

        return [
            'navigationTranslations' => $navigationTranslations,
        ];
    }
}
