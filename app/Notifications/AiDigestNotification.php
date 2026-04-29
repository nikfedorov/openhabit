<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Notifications\Channels\TelegramChannel;
use App\Notifications\Contracts\SendsTelegramNotification;
use App\Notifications\Messages\TelegramMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

/**
 * Queued notification that sends an AI-generated daily digest via Telegram.
 */
final class AiDigestNotification extends Notification implements SendsTelegramNotification, ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly ?string $content,
        public readonly string $date,
    ) {
        $this->onQueue('notifications');
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return list<class-string>
     */
    public function via(object $notifiable): array
    {
        return [TelegramChannel::class];
    }

    /**
     * Build the Telegram message representation.
     */
    public function toTelegram(object $notifiable): TelegramMessage
    {
        return TelegramMessage::create()
            ->parseMode('HTML')
            ->line(sprintf('<b>%s</b>', $this->date))
            ->line('')
            ->line(e($this->content ?? ''))
            ->webAppButton(__('telegram.open_app_plain'), url('/telegram-miniapp'));
    }
}
