<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use App\Actions\ResolvePremiumStateAction;
use App\Http\Resources\NavigationTranslationResource;
use App\Http\Resources\Settings\AiToneResource;
use App\Http\Resources\UserSettingResource;
use App\Models\AiTone;
use App\Models\User;
use App\Services\LocaleService;
use DateTimeZone;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Container\Attributes\CurrentUser;

/**
 * Return the authenticated user's settings and page data.
 */
#[Group('Settings', weight: 2)]
final readonly class IndexController
{
    /**
     * Show the current user's settings along with available options.
     */
    public function show(#[CurrentUser] User $user, ResolvePremiumStateAction $resolvePremiumState): UserSettingResource
    {
        $premiumState = $resolvePremiumState->handle($user);

        return new UserSettingResource($user, $premiumState)
            ->additional([
                /**
                 * Tabbar translations.
                 */
                'navigationTranslations' => NavigationTranslationResource::make($user),

                /**
                 * All available locales for the locale select input, in the format:
                 */
                'locales' => LocaleService::all(),

                /**
                 * AI tones available for the user.
                 */
                'aiTones' => AiToneResource::collection(
                    AiTone::query()->where('is_active', true)->orderBy('sort_order')->get(),
                ),

                /**
                 * Whether the user has access to premium features.
                 */
                'hasPremium' => $premiumState->hasPremium,

                /**
                 * Translations for the settings view.
                 *
                 * @var array<string, string>
                 */
                'translations' => trans('settings'),

                /**
                 * Timezone options grouped by region for the timezone select input.
                 *
                 * @var array<string, array<string, string>>
                 */
                'timezones' => $this->buildTimezoneOptions(),
            ]);
    }

    /**
     * Build timezone options grouped by region.
     *
     * @return array<string, array<string, string>>
     */
    private function buildTimezoneOptions(): array
    {
        $grouped = [];

        foreach (DateTimeZone::listIdentifiers() as $timezone) {
            $parts = explode('/', $timezone, 2);
            $region = $parts[0];
            $city = $parts[1] ?? $timezone;

            if (in_array($region, ['UTC', 'GMT'], true)) {
                $grouped['UTC'][$timezone] = $timezone;

                continue;
            }

            $grouped[$region][$timezone] = str_replace('_', ' ', $city);
        }

        ksort($grouped);

        foreach ($grouped as &$group) {
            asort($group);
        }

        return $grouped;
    }
}
