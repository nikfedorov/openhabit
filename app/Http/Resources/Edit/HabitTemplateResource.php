<?php

declare(strict_types=1);

namespace App\Http\Resources\Edit;

use App\Models\CategoryTemplate;
use App\Models\HabitTemplate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A habit template with its category name.
 *
 * @mixin HabitTemplate
 *
 * @property-read CategoryTemplate $categoryTemplate
 */
final class HabitTemplateResource extends JsonResource
{
    /**
     * @return array{id: int, name: string, human_readable: string, iterations_required: int, category: string, sort_order: int}
     */
    public function toArray(Request $request): array
    {
        return [
            /**
             * Habit template identifier.
             *
             * @var int
             *
             * @example 1
             */
            'id' => $this->id,

            /**
             * Localized habit name.
             *
             * @var string
             *
             * @example "Morning Meditation"
             */
            'name' => $this->name,

            /**
             * Human-readable recurrence rule.
             *
             * @var string
             *
             * @example "Every day"
             */
            'human_readable' => $this->human_readable,

            /**
             * Number of iterations required per occurrence.
             *
             * @var int
             *
             * @example 1
             */
            'iterations_required' => $this->iterations_required,

            /**
             * Localized category name.
             *
             * @var string
             *
             * @example "Health & Fitness"
             */
            'category' => $this->categoryTemplate->name,

            /**
             * Sort order within the category.
             *
             * @var int
             *
             * @example 1
             */
            'sort_order' => $this->sort_order,
        ];
    }
}
