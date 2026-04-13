<?php

declare(strict_types=1);

namespace App\Http\Controllers\Edit;

use App\Http\Resources\Edit\EditHabitResource;
use App\Http\Resources\NavigationTranslationResource;
use App\Http\Resources\UserSettingResource;
use App\Models\Habit;
use App\Models\User;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * List all habits for the edit page.
 */
#[Group('Edit', weight: 1)]
final readonly class IndexController
{
    /**
     * Get all habits for editing.
     *
     * Returns all habits including Franklin's Virtues (distinguishable via is_franklin_virtue),
     * along with translations for the edit page.
     */
    public function show(#[CurrentUser] User $user): AnonymousResourceCollection
    {
        /** @var Collection<int, Habit> $habits */
        $habits = $user->habits()
            ->with('category')
            ->ordered()
            ->get();

        return EditHabitResource::collection($habits)
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
                 * Translations for habit attributes, including frequency and day labels.
                 *
                 * @var array<string, string>
                 */
                'habitTranslations' => trans('habit'),
            ]);
    }
}
