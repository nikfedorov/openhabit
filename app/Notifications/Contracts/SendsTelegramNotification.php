<?php

declare(strict_types=1);

namespace App\Notifications\Contracts;

use App\Notifications\Messages\TelegramMessage;

/**
 * Contract for notifications that can be sent via Telegram.
 */
interface SendsTelegramNotification
{
    /**
     * Build the Telegram message representation.
     */
    public function toTelegram(object $notifiable): TelegramMessage;
}
