<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\CategoryTemplate;
use App\Models\Habit;
use App\Models\HabitTemplate;
use App\Models\User;
use Illuminate\Support\Collection;

final class HabitTemplateService
{
    /**
     * Apply all active templates (categories and habits) to a user.
     *
     * @return Collection<int, Habit>
     */
    public function applyTemplatesToUser(User $user): Collection
    {
        $categoryMapping = $this->createCategoriesFromTemplates($user);

        $templates = HabitTemplate::query()
            ->active()
            ->copyByDefault()
            ->ordered()
            ->get();

        return $templates->map(
            fn (HabitTemplate $template): Habit => $this->createHabitFromTemplate($user, $template, $categoryMapping)
        );
    }

    /**
     * Create categories from templates for a user.
     *
     * @return array<int, int> Mapping of category_template_id => category_id
     */
    public function createCategoriesFromTemplates(User $user): array
    {
        $categoryTemplates = CategoryTemplate::query()
            ->active()
            ->copyByDefault()
            ->ordered()
            ->get();

        $mapping = [];
        foreach ($categoryTemplates as $template) {
            $category = $user->categories()->create($template->toCategoryArray());
            $mapping[$template->id] = $category->id;
        }

        return $mapping;
    }

    /**
     * Copy a single template to a user without category.
     */
    public function copyTemplateToUser(User $user, HabitTemplate $template): Habit
    {
        return $this->createHabitFromTemplate($user, $template);
    }

    /**
     * Create a habit from a template for a specific user.
     *
     * @param  array<int, int>  $categoryMapping  Mapping of category_template_id => category_id
     */
    public function createHabitFromTemplate(User $user, HabitTemplate $template, array $categoryMapping = []): Habit
    {
        $habitData = $template->toHabitArray();

        if (isset($categoryMapping[$template->category_template_id])) {
            $habitData['category_id'] = $categoryMapping[$template->category_template_id];
        }

        return $user->habits()->create($habitData);
    }
}
