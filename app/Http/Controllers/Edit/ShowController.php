<?php

declare(strict_types=1);

namespace App\Http\Controllers\Edit;

use App\Http\Requests\Edit\ShowHabitRequest;
use App\Http\Resources\Edit\HabitFormResource;
use App\Http\Resources\NavigationTranslationResource;
use App\Http\Resources\UserSettingResource;
use App\Models\Habit;
use App\Models\User;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Container\Attributes\CurrentUser;

/**
 * Get a single habit for the edit form.
 */
#[Group('Edit', weight: 1)]
final readonly class ShowController
{
    /**
     * Get habit form data.
     *
     * Returns the habit with parsed RRule data for the edit form,
     * along with translations and settings needed by the habit form.
     */
    public function show(ShowHabitRequest $request, Habit $habit, #[CurrentUser] User $user): HabitFormResource
    {
        $habit->load('notifications');

        return new HabitFormResource($habit)
            ->additional([
                /** Tabbar translations. */
                'navigationTranslations' => NavigationTranslationResource::make($user),

                /** Settings for the current user. */
                'settings' => UserSettingResource::make($user),

                /**
                 * Translations for habit attributes, including frequency and day labels.
                 *
                 * @var array<string, string>
                 */
                'habitTranslations' => trans('habit'),
            ]);
    }
}
