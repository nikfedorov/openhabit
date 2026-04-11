<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * User-level settings shared across all pages.
 *
 * @property-read User $resource
 */
final class CommonResource extends JsonResource
{
    /**
     * @return array{locale: string, navigationTranslations: array<string, string>, settings: array{theme: string}}
     */
    public function toArray(Request $request): array
    {
        /** @var array<string, string> $navigationTranslations */
        $navigationTranslations = trans('navigation');

        return [
            'locale' => app()->getLocale(),
            'navigationTranslations' => $navigationTranslations,
            'settings' => new UserSettingResource($this->resource)->toArray($request),
        ];
    }
}
