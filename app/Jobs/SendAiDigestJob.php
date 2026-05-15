<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Actions\GenerateAiDigestAction;
use App\Models\AiDigest;
use App\Models\User;
use App\Notifications\AiDigestNotification;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Attributes\Timeout;
use Illuminate\Queue\Attributes\Tries;
use Illuminate\Queue\Attributes\UniqueFor;
use Illuminate\Support\Facades\Log;

/**
 * Generates an AI digest for a user and sends the notification.
 */
#[Timeout(180)]
#[Tries(1)]
#[UniqueFor(1800)]
final class SendAiDigestJob implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly int $userId,
    ) {
        $this->onQueue('ai-digests');
    }

    public function uniqueId(): string
    {
        return (string) $this->userId;
    }

    public function handle(GenerateAiDigestAction $generateAiDigest): void
    {
        $user = User::query()
            ->find($this->userId);

        if (! $user instanceof User) {
            Log::warning('SendAiDigestJob: user not found', ['user_id' => $this->userId]);

            return;
        }

        $digest = $generateAiDigest->handle($user);

        if ($digest instanceof AiDigest) {
            /** @var Carbon $date */
            $date = $digest->date->locale($user->preferredLocale());
            $dateFormatted = $date->isoFormat('LL, dddd');
            $user->notify(new AiDigestNotification($digest->content, $dateFormatted));
        }
    }

    /**
     * @return list<string>
     */
    public function tags(): array
    {
        return ['ai-digest', 'user:'.$this->userId];
    }
}
