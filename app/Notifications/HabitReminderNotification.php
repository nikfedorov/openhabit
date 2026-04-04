<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Habit;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

final class HabitReminderNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly Habit $habit,
    ) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }
}
