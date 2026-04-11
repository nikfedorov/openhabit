<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Enums\Theme;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * User-level application settings.
 *
 * Included as additional top-level data in Track and View responses.
 *
 * @property-read User $resource
 */
final class UserSettingResource extends JsonResource
{
    /**
     * @return array{locale: string, theme: string, moveCompletedToEnd: bool}
     */
    public function toArray(Request $request): array
    {
        return [
            /**
             * Current locale code.
             *
             * @var string
             *
             * @example "en"
             */
            'locale' => app()->getLocale(),

            /**
             * UI theme preference.
             *
             * @var string
             *
             * @example "system"
             */
            'theme' => ($this->resource->theme ?? Theme::System)->value,

            /**
             * Whether completed habits are moved to bottom.
             *
             * @var bool
             */
            'moveCompletedToEnd' => $this->resource->move_completed_to_end,
        ];
    }
}
