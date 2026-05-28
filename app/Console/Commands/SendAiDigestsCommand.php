<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\ResolvePremiumStateAction;
use App\Jobs\SendAiDigestJob;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

#[Description('Generate and send AI daily digests via Telegram')]
#[Signature('app:send-ai-digests')]
final class SendAiDigestsCommand extends Command
{
    public function handle(ResolvePremiumStateAction $resolvePremiumState): int
    {
        $users = $this->getEligibleUsers($resolvePremiumState);

        foreach ($users as $user) {
            dispatch(new SendAiDigestJob($user->id));
        }

        $this->info('AI digest jobs dispatched: '.$users->count());

        return self::SUCCESS;
    }

    /**
     * Get users eligible for AI digest right now.
     *
     * @return Collection<int, User>
     */
    private function getEligibleUsers(ResolvePremiumStateAction $resolvePremiumState): Collection
    {
        return User::query()
            ->select(['id', 'timezone', 'ai_digest_time', 'subscription_expires_at', 'created_at', 'trial_banner_dismissed_at'])
            ->whereNotNull('ai_digest_time')
            ->canReceiveTelegram()
            ->with('lastDigest:ai_digests.id,ai_digests.user_id,ai_digests.created_at')
            ->get()
            ->filter(fn (User $user): bool => $this->isEligible($user, $resolvePremiumState));
    }

    /**
     * A user is eligible when, in their timezone, all hold:
     *  - Has an active premium/trial.
     *  - Current time is at or past their configured digest time.
     *  - They have not already been digested today.
     *  - They had trackable activity yesterday (completion or daily note).
     */
    private function isEligible(User $user, ResolvePremiumStateAction $resolvePremiumState): bool
    {
        if (! $resolvePremiumState->handle($user)->hasPremium) {
            return false;
        }

        $userNow = CarbonImmutable::now($user->timezone ?? 'UTC');

        if ($userNow->format('H:i') < (string) $user->ai_digest_time) {
            return false;
        }

        if ($user->lastDigest?->created_at?->timezone($userNow->timezone)->isSameDay($userNow) === true) {
            return false;
        }

        return $this->hadActivityYesterday($user, $userNow->subDay()->toDateString());
    }

    /**
     * At least one habit completion or a non-empty daily note on the given date.
     */
    private function hadActivityYesterday(User $user, string $yesterday): bool
    {
        if ($user->habitCompletions()->whereDate('completed_at', $yesterday)->exists()) {
            return true;
        }

        return $user->dailyNotes()
            ->where('date', $yesterday)
            ->whereNotNull('content')
            ->where('content', '!=', '')
            ->exists();
    }
}
