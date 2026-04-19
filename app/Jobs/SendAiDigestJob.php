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
use Illuminate\Support\Facades\Log;

/**
 * Generates an AI digest for a user and sends the notification.
 */
final class SendAiDigestJob implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    /**
     * Abort the job after 90 seconds.
     * Must stay below the supervisor timeout (120 s) which itself must stay
     * below retry_after (180 s) in config/queue.php.
     */
    public int $timeout = 90;

    /**
     * Release the unique lock after 30 minutes (safety net).
     */
    public int $uniqueFor = 1800;

    public function __construct(
        public readonly string $userId,
    ) {
        $this->onQueue('ai-digests');
    }

    public function uniqueId(): string
    {
        return $this->userId;
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
