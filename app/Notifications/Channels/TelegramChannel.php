<?php

declare(strict_types=1);

namespace App\Notifications\Channels;

use App\Actions\Telegram\FlagTelegramDeliveryFailure;
use App\Models\User;
use App\Notifications\Contracts\SendsTelegramNotification;
use Illuminate\Notifications\Notification;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Exceptions\TelegramException;

/**
 * Laravel notification channel that sends messages via Telegram using Nutgram.
 */
final readonly class TelegramChannel
{
    public function __construct(private Nutgram $bot, private FlagTelegramDeliveryFailure $flag) {}

    /**
     * Send the given notification.
     */
    public function send(object $notifiable, Notification $notification): void
    {
        if (! $notification instanceof SendsTelegramNotification) {
            return;
        }

        if (! method_exists($notifiable, 'routeNotificationFor')) {
            return;
        }

        /** @var string|null $chatId */
        $chatId = $notifiable->routeNotificationFor('telegram', $notification);

        if ($chatId === null || $chatId === '') {
            return;
        }

        $message = $notification->toTelegram($notifiable);

        try {
            $this->bot->sendMessage(
                text: $message->getText(),
                chat_id: $chatId,
                parse_mode: $message->getParseMode(),
                reply_markup: $message->getReplyMarkup(),
            );
        } catch (TelegramException $telegramException) {
            $this->handleTelegramException($telegramException, $notifiable);
        }
    }

    /**
     * Handle Telegram API errors by flagging the user accordingly.
     * Known delivery failures flag the user and swallow the exception;
     * anything else is re-thrown so the queue worker can retry or fail.
     */
    private function handleTelegramException(TelegramException $e, object $notifiable): void
    {
        throw_unless($notifiable instanceof User, $e);
        throw_unless($this->flag->handle($e, fn (): User => $notifiable), $e);
    }
}
