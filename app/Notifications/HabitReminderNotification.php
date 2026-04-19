<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Habit;
use App\Notifications\Channels\TelegramChannel;
use App\Notifications\Contracts\SendsTelegramNotification;
use App\Notifications\Messages\TelegramMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\WebApp\WebAppInfo;

/**
 * Queued notification that reminds a user about a habit via Telegram.
 */
final class HabitReminderNotification extends Notification implements SendsTelegramNotification, ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Habit $habit,
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
        $message = TelegramMessage::create()
            ->parseMode('HTML')
            ->line('<b>'.__('telegram.reminder_header').'</b>')
            ->line($this->habit->name);

        if ($this->habit->description !== null && $this->habit->description !== '') {
            $message
                ->line('')
                ->line(__('telegram.description', ['text' => $this->habit->description]));
        }

        return $message->inlineRow(
            InlineKeyboardButton::make(text: __('telegram.mark_as_done'), callback_data: 'complete_habit:'.$this->habit->id),
            InlineKeyboardButton::make(text: __('telegram.open_app'), web_app: WebAppInfo::make(url('/telegram-miniapp'))),
        );
    }
}
