<?php

declare(strict_types=1);

namespace App\Http\Controllers\View;

use App\Actions\View\IndexAction;
use App\Http\Requests\View\IndexRequest;
use App\Http\Resources\NavigationTranslationResource;
use App\Http\Resources\UserSettingResource;
use App\Http\Resources\ViewResource;
use App\Models\User;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Container\Attributes\CurrentUser;

/**
 * Visualization and analytics data.
 */
#[Group('View', weight: 1)]
final readonly class IndexController
{
    public function __construct(private IndexAction $viewAction) {}

    /**
     * Get visualization data.
     *
     * Returns data for the week grid, year heatmap, or life (memento mori)
     * visualization depending on the selected tab.
     */
    public function show(IndexRequest $request, #[CurrentUser] User $user): ViewResource
    {
        return new ViewResource($this->viewAction->handle(
            $user,
            $request->selectedTab(),
            $request->selectedWeek(),
            $request->selectedYear(),
        ))->additional([
            'navigationTranslations' => NavigationTranslationResource::make($user),
            'settings' => UserSettingResource::make($user),
        ]);
    }
}
