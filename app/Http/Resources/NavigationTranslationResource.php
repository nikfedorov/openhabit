<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Localized navigation labels for tab bar.
 */
final class NavigationTranslationResource extends JsonResource
{
    /**
     * @return array<string, string>
     */
    public function toArray(Request $request): array
    {
        /** @var array<string, string> $translations */
        $translations = trans('navigation');

        return $translations;
    }
}
