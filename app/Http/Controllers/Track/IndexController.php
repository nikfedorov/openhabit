<?php

declare(strict_types=1);

namespace App\Http\Controllers\Track;

use App\Actions\Track\IndexAction;
use App\Http\Requests\Track\IndexRequest;
use App\Http\Resources\CommonResource;
use App\Http\Resources\TrackResource;
use App\Http\Resources\UserSettingResource;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;

final readonly class IndexController
{
    public function __construct(private IndexAction $indexTrack) {}

    public function show(IndexRequest $request, #[CurrentUser] User $user): TrackResource
    {
        return new TrackResource($this->indexTrack->handle($user, $request->selectedDate()))
            ->additional([
                ...new CommonResource($user)->resolve(),
                ...new UserSettingResource($user)->resolve(),
            ]);
    }
}
