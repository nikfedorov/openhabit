<?php

declare(strict_types=1);

namespace App\Http\Controllers\Edit;

use App\Http\Resources\NavigationTranslationResource;
use App\Http\Resources\UserSettingResource;
use App\Models\User;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\JsonResponse;

/**
 * Get translations and settings for the habit creation form.
 */
#[Group('Edit', weight: 1)]
final readonly class CreateController
{
    /**
     * Get habit creation form data.
     *
     * Returns translations and settings needed by the habit form
     * when creating a new habit.
     */
    public function show(#[CurrentUser] User $user): JsonResponse
    {
        return response()->json([
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
