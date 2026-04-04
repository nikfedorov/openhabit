<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\HabitCompletion;
use App\Services\StatService;

final readonly class HabitCompletionObserver
{
    public function __construct(private StatService $statService) {}

    public function created(HabitCompletion $completion): void
    {
        $this->recalculate($completion);
    }

    public function updated(HabitCompletion $completion): void
    {
        $this->recalculate($completion);
    }

    public function deleted(HabitCompletion $completion): void
    {
        $this->recalculate($completion);
    }

    private function recalculate(HabitCompletion $completion): void
    {
        $completion->loadMissing('user');
        $user = $completion->user;

        if ($user === null) {
            return;
        }

        $this->statService->recalculateForDate(
            $user,
            $completion->completed_at,
        );
    }
}
