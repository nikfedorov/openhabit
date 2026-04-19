<?php

declare(strict_types=1);

namespace App\Http\Controllers\View;

use App\Actions\View\GetWeekViewAction;
use App\Http\Requests\View\WeekRequest;
use App\Http\Resources\NavigationTranslationResource;
use App\Http\Resources\UserSettingResource;
use App\Http\Resources\View\WeekDayResource;
use App\Http\Resources\View\WeekGridHabitResource;
use App\Http\Resources\View\WeekViewResource;
use App\Models\User;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Support\Arr;

/**
 * Visualization and analytics data.
 */
#[Group('View', weight: 1)]
final readonly class WeekController
{
    public function __construct(private GetWeekViewAction $getWeekView) {}

    /**
     * Get week grid data.
     *
     * Returns the Franklin virtue grid for the requested week.
     */
    public function show(WeekRequest $request, #[CurrentUser] User $user): WeekViewResource
    {
        $data = $this->getWeekView->handle($user, $request->selectedWeek());

        return new WeekViewResource($data)
            ->additional([
                /** Tabbar translations. */
                'navigationTranslations' => NavigationTranslationResource::make($user),

                /** Settings for the current user. */
                'settings' => UserSettingResource::make($user),

                /**
                 * Translations for the week view, including virtue names and day labels.
                 *
                 * @var array<string, string>
                 */
                'translations' => $this->translations(),

                /**
                 * Week day headers for the grid.
                 */
                'days' => WeekDayResource::collection($data->days),

                /**
                 * Habit rows with daily completion statuses.
                 */
                'habits' => WeekGridHabitResource::collection($data->habits),
            ]);
    }

    /**
     * Get translations for the week view.
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
            'previous_week', 'next_week', 'this_week', 'current_week',
            'habits', 'franklins_virtues', 'done', 'partial', 'missed', 'future',
        ]);

        return $translations;
    }
}
