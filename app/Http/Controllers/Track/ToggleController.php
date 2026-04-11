<?php

declare(strict_types=1);

namespace App\Http\Controllers\Track;

use App\Actions\Track\IndexAction;
use App\Actions\Track\ToggleAction;
use App\Http\Requests\Track\ToggleRequest;
use App\Http\Resources\CommonResource;
use App\Http\Resources\TrackResource;
use App\Http\Resources\UserSettingResource;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;

final readonly class ToggleController
{
    public function __construct(
        private ToggleAction $toggleHabitCompletion,
        private IndexAction $indexTrack,
    ) {}

    public function store(ToggleRequest $request, #[CurrentUser] User $user): TrackResource
    {
        $this->toggleHabitCompletion->handle($user, $request->habitId(), $request->completionDate());

        return new TrackResource($this->indexTrack->handle($user, $request->completionDate()))
            ->additional([
                ...new CommonResource($user)->resolve(),
                ...new UserSettingResource($user)->resolve(),
            ]);
    }
}
