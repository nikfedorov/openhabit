<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Actions\ResolvePremiumStateAction;
use App\Data\PremiumState;
use App\Enums\Theme;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * User-level application settings.
 *
 * Included as additional top-level data in Track and View responses.
 *
 * @property-read User $resource
 */
final class UserSettingResource extends JsonResource
{
    public function __construct(mixed $resource, private ?PremiumState $premiumState = null)
    {
        parent::__construct($resource);
    }

    /**
     * @return array{
     *     locale: string,
     *     theme: string,
     *     moveCompletedToEnd: bool,
     *     timezone: string|null,
     *     dayStartsAt: string|null,
     *     birthdate: string|null,
     *     aiDigestTime: string|null,
     *     aiToneId: int|null,
     *     trial: TrialResource,
     *     telegramBotUsername: string|null,
     * }
     */
    public function toArray(Request $request): array
    {
        return [
            /**
             * Current locale code.
             *
             * @var string
             *
             * @example "en"
             */
            'locale' => app()->getLocale(),

            /**
             * UI theme preference.
             *
             * @var string
             *
             * @example "system"
             */
            'theme' => ($this->resource->theme ?? Theme::System)->value,

            /**
             * Whether completed habits are moved to bottom.
             *
             * @var bool
             */
            'moveCompletedToEnd' => $this->resource->move_completed_to_end,

            /**
             * IANA timezone identifier.
             *
             * @var string|null
             *
             * @example "Europe/London"
             */
            'timezone' => $this->resource->timezone,

            /**
             * Time of day when the "day" resets for habit tracking (HH:MM).
             *
             * @var string|null
             *
             * @example "03:00"
             */
            'dayStartsAt' => $this->resource->day_starts_at !== null
                ? mb_substr($this->resource->day_starts_at, 0, 5)
                : null,

            /**
             * User's date of birth.
             *
             * @var string|null
             *
             * @example "1990-01-15"
             */
            'birthdate' => $this->resource->birthdate?->format('Y-m-d'),

            /**
             * Scheduled time for the daily AI digest (HH:MM). Null means disabled.
             *
             * @var string|null
             *
             * @example "09:00"
             */
            'aiDigestTime' => $this->resource->ai_digest_time,

            /**
             * ID of the AI tone used for the digest.
             *
             * @var int|null
             */
            'aiToneId' => $this->resource->ai_tone_id,

            /**
             * User's long-term goal used as context for AI digest generation.
             *
             * @var string|null
             */
            'longTermGoal' => $this->resource->long_term_goal,

            /**
             * Trial banner and premium modal data.
             */
            'trial' => new TrialResource($this->premiumState()),

            /**
             * Telegram bot username for story sharing widget link.
             *
             * @var string|null
             */
            'telegramBotUsername' => Setting::getValue('telegram_bot_username'),
        ];
    }

    private function premiumState(): PremiumState
    {
        return $this->premiumState ??= resolve(ResolvePremiumStateAction::class)->handle($this->resource);
    }
}
