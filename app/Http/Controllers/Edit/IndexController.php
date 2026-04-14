<?php

declare(strict_types=1);

namespace App\Http\Controllers\Edit;

use App\Actions\Edit\GetHabitTemplatesAction;
use App\Actions\Edit\GetUserHabitsAction;
use App\Http\Resources\Edit\EditHabitResource;
use App\Http\Resources\Edit\HabitTemplateResource;
use App\Http\Resources\NavigationTranslationResource;
use App\Http\Resources\UserSettingResource;
use App\Models\User;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * List all habits for the edit page.
 */
#[Group('Edit', weight: 1)]
final readonly class IndexController
{
    public function __construct(
        private GetUserHabitsAction $getUserHabits,
        private GetHabitTemplatesAction $getHabitTemplates,
    ) {}

    /**
     * Get all habits for editing.
     *
     * Returns all habits including Franklin's Virtues (distinguishable via is_franklin_virtue),
     * along with translations for the edit page.
     */
    public function show(#[CurrentUser] User $user): AnonymousResourceCollection
    {
        return EditHabitResource::collection($this->getUserHabits->handle($user))
            ->additional([
                /** Tabbar translations. */
                'navigationTranslations' => NavigationTranslationResource::make($user),

                /** Settings for the current user. */
                'settings' => UserSettingResource::make($user),

                /**
                 * Translations for the edit view.
                 *
                 * @var array<string, string>
                 */
                'translations' => trans('edit'),

                /**
                 * Habit templates with category names.
                 */
                'templates' => HabitTemplateResource::collection(
                    $this->getHabitTemplates->handle()
                ),
            ]);
    }
}
