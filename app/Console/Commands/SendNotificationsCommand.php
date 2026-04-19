<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Habit;
use App\Models\HabitNotification;
use App\Models\User;
use App\Notifications\HabitReminderNotification;
use App\Services\RRuleService;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

final class SendNotificationsCommand extends Command
{
    /**
     * Notifications are eligible within this window (in minutes) after the scheduled time.
     */
    private const int TIME_WINDOW_MINUTES = 5;

    protected $signature = 'app:send-notifications';

    protected $description = 'Send habit reminder notifications via Telegram';

    public function handle(RRuleService $rruleService): int
    {
        $dispatched = 0;

        foreach ($this->getEligibleNotifications($rruleService) as $notification) {
            /** @var Habit $habit */
            $habit = $notification->habit;
            /** @var User $user */
            $user = $habit->user;

            // Mark as notified immediately to prevent duplicates on next run
            $notification->update(['last_notified_at' => now()]);

            $user->notify(new HabitReminderNotification($habit));
            $dispatched++;
        }

        $this->info('Notifications dispatched: '.$dispatched);

        return self::SUCCESS;
    }

    /**
     * Get all notifications that should be sent right now.
     *
     * Filters applied:
     *  1. DB: habit is active and user has a telegram_id
     *  2. PHP: current time in user's timezone falls within the 15-min window
     *  3. PHP: not already notified today in user's timezone
     *  4. PHP: habit is scheduled for today (RRule check)
     *
     * @return Collection<int, HabitNotification>
     */
    private function getEligibleNotifications(RRuleService $rruleService): Collection
    {
        $notifications = HabitNotification::query()
            ->select(['id', 'habit_id', 'time', 'is_active', 'last_notified_at'])
            ->with([
                'habit:id,user_id,name,description,rrule,is_active' => [
                    'user:id,telegram_id,timezone,locale',
                ],
            ])
            ->where('is_active', true)
            ->whereHas('habit', fn (Builder $q) => $q
                ->where('is_active', true)
                ->whereHas('user', fn (Builder $u) => $u
                    ->whereNotNull('telegram_id')
                    ->whereNull('telegram_bot_blocked_at')
                    ->whereNull('telegram_user_deleted_at')
                )
            )
            ->get();

        return $notifications->filter(function (HabitNotification $notification) use ($rruleService): bool {
            /** @var Habit $habit */
            $habit = $notification->habit;
            /** @var User $user */
            $user = $habit->user;

            $timezone = $user->timezone ?? 'UTC';
            $userNow = CarbonImmutable::now($timezone);

            // Check time window (0..14 min after scheduled time)
            if (! $this->isWithinTimeWindow($userNow->format('H:i'), $notification->time)) {
                return false;
            }

            // Exact per-timezone "already sent today" check
            if ($notification->last_notified_at !== null
                && $notification->last_notified_at->timezone($timezone)->isSameDay($userNow)) {
                return false;
            }

            // RRule schedule check
            if ($habit->rrule !== null && ! $rruleService->matchesDate($habit->rrule, $userNow)) {
                return false;
            }

            return true;
        });
    }

    /**
     * Check if current time falls within the notification's time window.
     *
     * Both parameters must be in HH:MM format.
     */
    private function isWithinTimeWindow(string $currentTime, string $notificationTime): bool
    {
        [$cH, $cM] = array_map(intval(...), explode(':', $currentTime));
        [$nH, $nM] = array_map(intval(...), explode(':', $notificationTime));

        $diff = ($cH * 60 + $cM) - ($nH * 60 + $nM);

        return $diff >= 0 && $diff < self::TIME_WINDOW_MINUTES;
    }
}
