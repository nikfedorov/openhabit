<?php

declare(strict_types=1);

namespace App\Http\Requests\Settings;

use App\Services\LocaleService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;
use Illuminate\Validation\Rules\In;
use Illuminate\Validation\Validator;

final class UpdateSettingsRequest extends FormRequest
{
    /**
     * @return array<string, array<int, ValidationRule|string|In|Exists>>
     */
    public function rules(): array
    {
        return [
            /**
             * UI theme preference.
             *
             * @example "dark"
             */
            'theme' => ['sometimes', 'string', Rule::in(['light', 'dark', 'system'])],

            /**
             * Display language code.
             *
             * @example "en"
             */
            'locale' => ['sometimes', 'string', Rule::in(LocaleService::codes())],

            /**
             * IANA timezone identifier.
             *
             * @example "Europe/London"
             */
            'timezone' => ['sometimes', 'string', 'timezone'],

            /**
             * User's date of birth.
             *
             * @example "1990-01-15"
             */
            'birthdate' => ['sometimes', 'nullable', 'date'],

            /**
             * Time of day when the "day" resets for habit tracking (HH:MM).
             *
             * @example "03:00"
             */
            'dayStartsAt' => ['sometimes', 'nullable', 'regex:/^([01]?\d|2[0-3]):[0-5]\d$/'],

            /**
             * Whether completed habits are moved to the bottom of the list.
             */
            'moveCompletedToEnd' => ['sometimes', 'boolean'],

            /**
             * Scheduled time for the daily AI digest (HH:MM). Null disables it.
             *
             * @example "09:00"
             */
            'aiDigestTime' => ['sometimes', 'nullable', 'regex:/^([01]?\d|2[0-3]):[0-5]\d$/'],

            /**
             * AI tone to use for the digest.
             *
             * @example 1
             */
            'aiToneId' => ['sometimes', 'nullable', 'integer', Rule::exists('ai_tones', 'id')->where('is_active', true)],
        ];
    }

    /**
     * Validate that enabling AI digest requires a premium subscription.
     *
     * @return array<int, Closure>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($this->filled('aiDigestTime') && ! $this->user()?->hasPremium()) {
                    $validator->errors()->add('aiDigestTime', __('This feature requires a premium subscription.'));
                }
            },
        ];
    }

    /**
     * Return only the validated fields that were present in the request.
     *
     * @return array{
     *     theme?: string,
     *     locale?: string,
     *     timezone?: string,
     *     birthdate?: string|null,
     *     dayStartsAt?: string|null,
     *     moveCompletedToEnd?: bool,
     *     aiDigestTime?: string|null,
     *     aiToneId?: int|null,
     * }
     */
    public function settingsData(): array
    {
        /** @var array{theme?: string, locale?: string, timezone?: string, birthdate?: string|null, dayStartsAt?: string|null, moveCompletedToEnd?: bool, aiDigestTime?: string|null, aiToneId?: int|null} */
        return $this->safe()->all();
    }
}
