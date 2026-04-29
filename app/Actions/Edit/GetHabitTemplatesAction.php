<?php

declare(strict_types=1);

namespace App\Actions\Edit;

use App\Models\CategoryTemplate;
use App\Models\HabitTemplate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Fetch active habit templates with their category names.
 *
 * Returns a flat list of visible habit templates ordered by
 * category sort_order, then template sort_order.
 */
final readonly class GetHabitTemplatesAction
{
    /**
     * Get visible habit templates with their categories eager-loaded.
     *
     * @return Collection<int, HabitTemplate>
     */
    public function handle(): Collection
    {
        return HabitTemplate::query()
            ->active()
            ->showInTemplates()
            ->whereHas('categoryTemplate', fn (Builder $q): Builder => $q->where('is_active', true))
            ->with('categoryTemplate')
            ->orderBy(
                CategoryTemplate::query()
                    ->select('sort_order')
                    ->whereColumn('category_templates.id', 'habit_templates.category_template_id')
                    ->limit(1),
            )
            ->ordered()
            ->get();
    }
}
