<?php

declare(strict_types=1);

namespace App\Actions\Telegram;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use SergiX44\Nutgram\Telegram\Exceptions\TelegramException;

/**
 * Checks if a TelegramException is a known delivery failure and, if so,
 * flags the user's account accordingly.
 *
 * Returns true when the exception is a known failure (caller should swallow it),
 * false otherwise (caller should re-throw).
 */
final readonly class FlagTelegramDeliveryFailure
{
    /**
     * Map of Telegram error substrings to the user column we flag on match.
     *
     * @var array<string, string>
     */
    private const array DELIVERY_FAILURE_MAP = [
        'bot was blocked by the user' => 'telegram_bot_blocked_at',
        'user is deactivated' => 'telegram_user_deleted_at',
        'chat not found' => 'telegram_user_deleted_at',
    ];

    /**
     * Returns true when the exception is a known delivery failure.
     * If a user is supplied, flags their account and logs the event.
     */
    public function handle(?User $user, TelegramException $e): bool
    {
        foreach (self::DELIVERY_FAILURE_MAP as $needle => $column) {
            if (str_contains($e->getMessage(), $needle)) {
                if ($user instanceof User) {
                    $user->updateQuietly([$column => now()]);
                    Log::info('Telegram: delivery failure', [
                        'user_id' => $user->id,
                        'reason' => $needle,
                    ]);
                }

                return true;
            }
        }

        return false;
    }
}
