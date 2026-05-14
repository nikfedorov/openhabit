<?php

declare(strict_types=1);

namespace App\Telegram\Handlers;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Exceptions\TelegramException;
use Throwable;

/**
 * Global Nutgram exception handler that swallows known Telegram delivery
 * failures and flags the affected user accordingly. Any other exception
 * is re-thrown so the caller can handle or report it.
 */
final readonly class DeliveryFailureHandler
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
     * @throws Throwable
     */
    public function __invoke(Nutgram $bot, Throwable $e): void
    {
        throw_unless($e instanceof TelegramException, $e);

        foreach (self::DELIVERY_FAILURE_MAP as $needle => $column) {
            if (str_contains($e->getMessage(), $needle)) {
                $userId = (string) $bot->userId();

                if ($userId !== '') {
                    User::query()
                        ->where('telegram_id', $userId)
                        ->update([$column => now()]);

                    Log::info('DeliveryFailureHandler: delivery failure', [
                        'telegram_id' => $userId,
                        'reason' => $needle,
                    ]);
                }

                return;
            }
        }

        throw $e;
    }
}
