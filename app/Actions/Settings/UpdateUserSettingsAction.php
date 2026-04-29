<?php

declare(strict_types=1);

namespace App\Actions\Settings;

use App\Jobs\SetTelegramMenuButtonJob;
use App\Models\User;
use Illuminate\Support\Str;

/**
 * Update a user's settings/preferences.
 *
 * Accepts a partial array of camelCase API fields and maps them to
 * snake_case database columns via Str::snake().
 */
final readonly class UpdateUserSettingsAction
{
    /**
     * Update the given user's settings.
     *
     * @param  array{
     *     theme?: string,
     *     locale?: string,
     *     timezone?: string,
     *     birthdate?: string|null,
     *     dayStartsAt?: string|null,
     *     moveCompletedToEnd?: bool,
     *     aiDigestTime?: string|null,
     *     aiToneId?: int|null,
     * }  $data
     */
    public function handle(User $user, array $data): void
    {
        // Convert camelCase API keys to snake_case column names (e.g. dayStartsAt → day_starts_at)
        $updates = [];
        foreach ($data as $key => $value) {
            $updates[Str::snake($key)] = $value;
        }

        // The `time` column requires HH:MM:SS; the API accepts the shorter HH:MM
        if (isset($updates['day_starts_at'])) {
            $updates['day_starts_at'] .= ':00';
        }

        $localeChanged = isset($updates['locale']) && $updates['locale'] !== $user->locale;

        $user->update($updates);

        if ($localeChanged && isset($user->telegram_id)) {
            dispatch(new SetTelegramMenuButtonJob($user->id));
        }
    }
}
