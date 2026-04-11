<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Enums\Theme;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Exposes user-level settings to the frontend.
 *
 * @property-read User $resource
 */
final class UserSettingResource extends JsonResource
{
    /**
     * @return array{settings: array{locale: string, theme: string, moveCompletedToEnd: bool}}
     */
    public function toArray(Request $request): array
    {
        return [
            'settings' => [
                'locale' => app()->getLocale(),
                'theme' => ($this->resource->theme ?? Theme::System)->value,
                'moveCompletedToEnd' => $this->resource->move_completed_to_end,
            ],
        ];
    }
}
