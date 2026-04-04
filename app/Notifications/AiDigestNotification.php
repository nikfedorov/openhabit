<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

final class AiDigestNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly ?string $content,
        public readonly string $date,
    ) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }
}
