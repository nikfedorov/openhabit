<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\SendAiDigestJob;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;

final class SendAiDigestsCommand extends Command
{
    protected $signature = 'app:send-ai-digests';

    protected $description = 'Generate and send AI daily digests via Telegram';

    public function handle(): int
    {
        $users = $this->getEligibleUsers();

        foreach ($users as $user) {
            dispatch(new SendAiDigestJob($user->id));
        }

        $this->info('AI digest jobs dispatched: '.$users->count());

        return self::SUCCESS;
    }

    /**
     * Get users eligible for AI digest right now.
     *
     * Filters:
     * 1. DB: ai_digest_time is set and telegram_id exists
     * 2. PHP: current time in user's timezone is at or past the scheduled digest time
     * 3. PHP: not already digested today in user's timezone
     *
     * @return Collection<int, User>
     */
    private function getEligibleUsers(): Collection
    {
        $users = User::query()
            ->select(['id', 'timezone', 'ai_digest_time', 'subscription_expires_at', 'created_at'])
            ->whereNotNull('ai_digest_time')
            ->canReceiveTelegram()
            ->with('lastDigest:ai_digests.id,ai_digests.user_id,ai_digests.created_at')
            ->get();

        return $users->filter(function (User $user): bool {
            if (! $user->hasPremium()) {
                return false;
            }

            $timezone = $user->timezone ?? 'UTC';
            $userNow = CarbonImmutable::now($timezone);

            // Check if digest time has passed today
            if ($user->ai_digest_time === null || ! $this->isAfterDigestTime($userNow->format('H:i'), $user->ai_digest_time)) {
                return false;
            }

            // Check if already digested today (user's timezone)
            $lastDigest = $user->lastDigest;
            if ($lastDigest?->created_at !== null
                && $lastDigest->created_at->timezone($timezone)->isSameDay($userNow)) {
                return false;
            }

            return true;
        });
    }

    /**
     * Check if current time is at or past the scheduled digest time.
     */
    private function isAfterDigestTime(string $currentTime, string $digestTime): bool
    {
        return Date::createFromFormat('H:i', $currentTime) >= Date::createFromFormat('H:i', $digestTime);
    }
}
