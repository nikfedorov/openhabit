<?php

declare(strict_types=1);

namespace App\Http\Controllers\View;

use App\Actions\View\GetYearViewAction;
use App\Http\Requests\View\YearRequest;
use App\Http\Resources\NavigationTranslationResource;
use App\Http\Resources\UserSettingResource;
use App\Http\Resources\View\YearActivityResource;
use App\Http\Resources\View\YearViewResource;
use App\Models\User;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Support\Arr;

/**
 * Visualization and analytics data.
 */
#[Group('View', weight: 1)]
final readonly class YearController
{
    public function __construct(private GetYearViewAction $getYearView) {}

    /**
     * Get year heatmap data.
     *
     * Returns weekly activity data for the selected life year (age).
     */
    public function show(YearRequest $request, #[CurrentUser] User $user): YearViewResource
    {
        $data = $this->getYearView->handle($user, $request->selectedYear());

        return new YearViewResource($data)
            ->additional([
                /** Tabbar translations. */
                'navigationTranslations' => NavigationTranslationResource::make($user),

                /** Settings for the current user. */
                'settings' => UserSettingResource::make($user),

                /**
                 * Translations for the year view tab.
                 *
                 * @var array<string, string>
                 */
                'translations' => $this->translations(),

                /**
                 * Weekly activity data for the selected life year.
                 */
                'activityData' => $data->activityData !== null
                    ? YearActivityResource::collection($data->activityData)
                    : null,
            ]);
    }

    /**
     * Get translations for the year view.
     *
     * @return array<string, string>
     */
    private function translations(): array
    {
        /** @var array<string, string> $allTranslations */
        $allTranslations = trans('view');

        /** @var array<string, string> $translations */
        $translations = Arr::only($allTranslations, [
            'week', 'year', 'life',
            'previous_year', 'next_year', 'this_year', 'current_year', 'age',
            'less', 'more', 'future', 'each_square_week',
            'set_birthdate', 'to_see_year_visualization',
        ]);

        return $translations;
    }
}
