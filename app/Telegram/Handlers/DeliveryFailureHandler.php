<?php

declare(strict_types=1);

namespace App\Telegram\Handlers;

use App\Actions\Telegram\FlagTelegramDeliveryFailure;
use App\Models\User;
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
    public function __construct(private FlagTelegramDeliveryFailure $flag) {}

    /**
     * @throws Throwable
     */
    public function __invoke(Nutgram $bot, Throwable $e): void
    {
        throw_unless($e instanceof TelegramException, $e);

        $userId = (string) $bot->userId();
        $user = $userId !== '' ? User::query()->where('telegram_id', $userId)->first() : null;

        throw_unless($this->flag->handle($user, $e), $e);
    }
}
