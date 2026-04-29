<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Attributes\Timeout;
use Illuminate\Queue\Attributes\Tries;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Exceptions\TelegramException;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;

/**
 * Sends an error alert message to a Telegram channel via Nutgram.
 *
 * Dispatched asynchronously by TelegramMonologHandler so error reporting
 * never blocks the request or exception handler.
 */
#[Timeout(15)]
#[Tries(2)]
final class SendTelegramErrorAlertJob implements ShouldQueue
{
    use Queueable;

    /**
     * @param  string  $chatId  Telegram chat / channel ID to send to.
     * @param  string  $text  HTML-formatted message text.
     */
    public function __construct(
        public readonly string $chatId,
        public readonly string $text,
    ) {
        $this->onQueue('notifications');
    }

    /**
     * Send the message using Nutgram.
     * Telegram API errors are intentionally swallowed so the error queue
     * does not retry alerts that the API will never accept.
     */
    public function handle(Nutgram $bot): void
    {
        if ($this->chatId === '' || $this->text === '') {
            return;
        }

        try {
            $bot->sendMessage(
                text: $this->text,
                chat_id: $this->chatId,
                parse_mode: ParseMode::HTML,
            );
        } catch (TelegramException) {
            // Swallow: a failed alert must not generate additional noise.
        }
    }
}
