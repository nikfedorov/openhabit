<?php

declare(strict_types=1);

namespace App\Actions\View;

use App\Data\View\WeekData;
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
        $weekStart = $weekStartInput !== null
            ? Date::parse($weekStartInput)->startOfWeek()
            : now()->startOfWeek();

        $weekEnd = $weekStart->copy()->endOfWeek();
        $habits = $this->getHabits($user, $weekStart, $weekEnd);

        $gridData = $this->habitActivityService->getFranklinGridData($habits, $weekStart);

        return new WeekData(
            start: $weekStart->toDateString(),
            end: $weekEnd->toDateString(),
            startFormatted: $weekStart->isoFormat('MMM D'),
            endFormatted: $weekEnd->isoFormat('MMM D'),
            endFormattedFull: $weekEnd->isoFormat('ll'),
            year: $weekStart->isoFormat('YYYY'),
            isCurrent: $weekStart->toDateString() === now()->startOfWeek()->toDateString(),
            days: $gridData->days,
            habits: $gridData->habits,
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
}
