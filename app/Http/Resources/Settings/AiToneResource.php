<?php

declare(strict_types=1);

namespace App\Http\Resources\Settings;

use App\Models\AiTone;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * AI tone option for the settings page.
 *
 * @mixin AiTone
 */
final class AiToneResource extends JsonResource
{
    /**
     * @return array{id: int, name: string, description: string|null, icon: string}
     */
    public function toArray(Request $request): array
    {
        return [
            /**
             * The AI tone's unique identifier, used for saving the user's selection.
             *
             * @example 1
             */
            'id' => $this->id,

            /**
             * The display name of the AI tone.
             *
             * @example "Friendly"
             *
             * @var string
             */
            'name' => $this->name,

            /**
             * A description of the AI tone's style or personality.
             *
             * @example "Provides responses in a warm and approachable manner."
             *
             * @var string|null
             */
            'description' => $this->description,

            /**
             * The name of the icon associated with this AI tone, used for display in the UI.
             *
             * @example "sun"
             */
            'icon' => $this->icon,
        ];
    }
}
