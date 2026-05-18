<?php

declare(strict_types=1);

namespace App\Actions\View;

use App\Data\View\WeekData;
use App\Models\AiDigest;
use App\Models\Habit;
use App\Models\User;
use App\Services\HabitActivityService;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Date;

/**
 * Load week grid data for the View page.
 */
final readonly class GetWeekViewAction
{
    public function __construct(
        private HabitActivityService $habitActivityService,
    ) {}

    public function handle(User $user, ?string $weekStartInput = null): WeekData
    {
        $today = $user->currentDate();
        $weekStart = $weekStartInput !== null
            ? Date::parse($weekStartInput, $user->timezone ?? 'UTC')->startOfWeek()
            : $today->startOfWeek();

        $weekEnd = $weekStart->copy()->endOfWeek();
        $habits = $this->getHabits($user, $weekStart, $weekEnd);

        $gridData = $this->habitActivityService->getFranklinGridData($habits, $weekStart, $today);

        return new WeekData(
            start: $weekStart->toDateString(),
            end: $weekEnd->toDateString(),
            startFormatted: $weekStart->isoFormat('MMM D'),
            endFormatted: $weekEnd->isoFormat('MMM D'),
            endFormattedFull: $weekEnd->isoFormat('ll'),
            year: $weekStart->isoFormat('YYYY'),
            isCurrent: $weekStart->toDateString() === $today->startOfWeek()->toDateString(),
            days: $gridData->days,
            habits: $gridData->habits,
            aiDigests: $this->getAiDigests($user, $weekStart->toDateString(), $weekEnd->toDateString()),
        );
    }

    /**
     * @return EloquentCollection<int, Habit>
     */
    private function getHabits(User $user, CarbonInterface $weekStart, CarbonInterface $weekEnd): EloquentCollection
    {
        return $user->habits()
            ->activeOrCompletedDuring($weekStart, $weekEnd)
            ->with('category')
            ->ordered()
            ->get();
    }

    /**
     * Get AI digests for the given week range.
     *
     * @return EloquentCollection<int, AiDigest>
     */
    private function getAiDigests(User $user, string $weekStart, string $weekEnd): EloquentCollection
    {
        return $user->aiDigests()
            ->select(['id', 'user_id', 'date', 'content'])
            ->whereBetween('date', [$weekStart, $weekEnd])
            ->orderBy('date')
            ->get();
    }
}
