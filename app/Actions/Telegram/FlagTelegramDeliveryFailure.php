<?php

declare(strict_types=1);

namespace App\Actions\Telegram;

use App\Models\User;
use Closure;
use Illuminate\Support\Facades\Log;
use SergiX44\Nutgram\Telegram\Exceptions\TelegramException;

/**
 * Detects known Telegram delivery failures (blocked bot, deactivated user,
 * missing chat) and flags the affected user's account.
 *
 * Returns true when the exception is a known failure so the caller can
 * swallow it; false otherwise so the caller can re-throw.
 */
final readonly class FlagTelegramDeliveryFailure
{
    /**
     * Telegram error message substrings mapped to the user column we flag.
     *
     * @var array<string, string>
     */
    private const array DELIVERY_FAILURE_MAP = [
        'bot was blocked by the user' => 'telegram_bot_blocked_at',
        'user is deactivated' => 'telegram_user_deleted_at',
        'chat not found' => 'telegram_user_deleted_at',
    ];

    /**
     * The user resolver is only invoked when the exception matches a known
     * failure, so callers can defer (potentially expensive) DB lookups.
     *
     * @param  Closure(): ?User  $resolveUser
     */
    public function handle(TelegramException $e, Closure $resolveUser): bool
    {
        foreach (self::DELIVERY_FAILURE_MAP as $needle => $column) {
            if (! str_contains($e->getMessage(), $needle)) {
                continue;
            }

            $user = $resolveUser();

            if ($user instanceof User) {
                $user->updateQuietly([$column => now()]);
                Log::info('Telegram: delivery failure', [
                    'user_id' => $user->id,
                    'reason' => $needle,
                ]);
            }

            return true;
        }

        return false;
    }
}
