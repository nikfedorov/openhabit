<?php

declare(strict_types=1);

namespace App\Http\Controllers\Track;

use App\Actions\Track\IndexAction;
use App\Http\Requests\Track\IndexRequest;
use App\Http\Resources\NavigationTranslationResource;
use App\Http\Resources\Track\HabitActivityDataResource;
use App\Http\Resources\Track\HabitResource;
use App\Http\Resources\Track\TrackResource;
use App\Http\Resources\UserSettingResource;
use App\Models\User;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Container\Attributes\CurrentUser;

/**
 * Retrieve daily habits and tracking data.
 */
#[Group('Track', weight: 0)]
final readonly class IndexController
{
    public function __construct(private IndexAction $indexTrack) {}

    /**
     * Get daily tracking data.
     *
     * Returns habits, completion progress, daily note, and activity heatmap data
     * for a specific date. Defaults to today when no date is provided.
     */
    public function show(IndexRequest $request, #[CurrentUser] User $user): TrackResource
    {
        $data = $this->indexTrack->handle($user, $request->selectedDate());

        return new TrackResource($data)
            ->additional([
                'navigationTranslations' => NavigationTranslationResource::make($user),
                'settings' => UserSettingResource::make($user),
                'habits' => HabitResource::collection($data->habits),
                'activityData' => HabitActivityDataResource::collection($data->activityData),
            ]);
    }
}
