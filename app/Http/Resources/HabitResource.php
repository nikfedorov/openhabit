<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Habit;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Habit
 */
final class HabitResource extends JsonResource
{
    /**
     * @return array{id: int, name: string, description: string|null, iterations_required: int, is_completed: bool, current_iteration: int, sort_order: int}
     */
    public function toArray(Request $request): array
    {
        /** @var Habit $habit */
        $habit = $this->resource;
        /** @var int $currentIteration */
        $currentIteration = $habit->getAttribute('current_iteration_for_date') ?? 0;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'iterations_required' => $this->iterations_required,
            'is_completed' => $currentIteration >= $this->iterations_required,
            'current_iteration' => $currentIteration,
            'sort_order' => $this->sort_order,
        ];
    }
}
