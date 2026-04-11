<?php

declare(strict_types=1);

namespace App\Http\Controllers\Track;

use App\Actions\Track\IndexAction;
use App\Actions\Track\ToggleAction;
use App\Http\Requests\Track\ToggleRequest;
use App\Http\Resources\HabitActivityDataResource;
use App\Http\Resources\HabitResource;
use App\Http\Resources\NavigationTranslationResource;
use App\Http\Resources\TrackResource;
use App\Http\Resources\UserSettingResource;
use App\Models\User;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Container\Attributes\CurrentUser;

/**
 * Toggle habit completion for a specific date.
 */
#[Group('Track', weight: 0)]
final readonly class ToggleController
{
    public function __construct(
        private ToggleAction $toggleHabitCompletion,
        private IndexAction $indexTrack,
    ) {}

    /**
     * Toggle habit completion.
     *
     * Marks a habit as completed (or uncompleted) for a given date.
     * Returns the refreshed daily tracking data.
     */
    public function store(ToggleRequest $request, #[CurrentUser] User $user): TrackResource
    {
        $this->toggleHabitCompletion->handle($user, $request->habitId(), $request->completionDate());

        $data = $this->indexTrack->handle($user, $request->completionDate());

        return new TrackResource($data)
            ->additional([
                'habits' => HabitResource::collection($data['habits']),
                'activityData' => HabitActivityDataResource::collection($data['activityData']),
                'navigationTranslations' => NavigationTranslationResource::make($user),
                'settings' => UserSettingResource::make($user),
            ]);
    }
}
