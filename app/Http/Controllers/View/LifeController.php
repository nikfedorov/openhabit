<?php

declare(strict_types=1);

namespace App\Http\Controllers\View;

use App\Actions\View\LifeAction;
use App\Http\Resources\NavigationTranslationResource;
use App\Http\Resources\UserSettingResource;
use App\Http\Resources\View\LifeActivityResource;
use App\Http\Resources\View\LifeViewResource;
use App\Models\User;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

/**
 * Visualization and analytics data.
 */
#[Group('View', weight: 1)]
final readonly class LifeController
{
    public function __construct(private LifeAction $lifeAction) {}

    /**
     * Get life (memento mori) grid data.
     *
     * Returns yearly activity data and life statistics for the memento mori visualization.
     */
    public function show(Request $request, #[CurrentUser] User $user): LifeViewResource
    {
        $data = $this->lifeAction->handle($user);

        return new LifeViewResource($data)
            ->additional([
                /** Tabbar translations. */
                'navigationTranslations' => NavigationTranslationResource::make($user),

                /** Settings for the current user. */
                'settings' => UserSettingResource::make($user),

                /**
                 * Translations for the life view tab.
                 *
                 * @var array<string, string>
                 */
                'translations' => $this->translations(),

                /**
                 * Yearly activity data for the life heatmap.
                 */
                'activityData' => $data->activityData !== null
                    ? LifeActivityResource::collection($data->activityData)
                    : null,
            ]);
    }

    /**
     * Get translations for the life view.
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
            'memento_mori', 'years_old', 'years_left', 'weeks_lived',
            'less', 'more', 'future', 'each_square_year', 'seneca_quote', 'seneca_author',
            'set_birthdate', 'to_see_life_visualization',
        ]);

        return $translations;
    }
}
