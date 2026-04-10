<?php

declare(strict_types=1);

namespace App\Actions\View;

use App\Models\Habit;
use App\Models\User;
use App\Services\HabitActivityService;
use App\Services\LifeGridService;
use App\Services\LifeYearCalculator;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Date;

/**
 * Load all data for the View page (week grid, year grid, life grid).
 */
final readonly class IndexAction
{
    public function __construct(
        private HabitActivityService $habitActivityService,
        private LifeGridService $lifeGridService,
        private LifeYearCalculator $lifeYearCalculator,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function handle(User $user, string $tab = 'week', ?string $weekStartInput = null, ?int $selectedYear = null): array
    {
        $weekStart = $weekStartInput !== null
            ? Date::parse($weekStartInput)->startOfWeek()
            : now()->startOfWeek();

        $weekEnd = $weekStart->copy()->endOfWeek();

        /** @var CarbonInterface|null $birthdate */
        $birthdate = $user->birthdate;
        $currentAge = $birthdate instanceof CarbonInterface
            ? $this->lifeYearCalculator->getCurrentAge($birthdate)
            : null;

        $selectedYear ??= $currentAge;

        $habits = $this->getHabits($user, $weekStart, $weekEnd);
        $lifeData = $this->lifeGridService->getLifeData($user, $birthdate, $selectedYear);

        return [
            'tab' => $tab,
            'weekStart' => $weekStart->toDateString(),
            'weekEnd' => $weekEnd->toDateString(),
            'weekStartFormatted' => $weekStart->isoFormat('MMM D'),
            'weekEndFormatted' => $weekEnd->isoFormat('MMM D'),
            'weekEndFormattedFull' => $weekEnd->isoFormat('ll'),
            'weekYear' => $weekStart->isoFormat('YYYY'),
            'isCurrentWeek' => $weekStart->toDateString() === now()->startOfWeek()->toDateString(),
            'franklinGrid' => $this->habitActivityService->getFranklinGridData($habits, $weekStart),
            'selectedYear' => $selectedYear,
            'birthdate' => $birthdate?->toDateString(),
            'currentAge' => $currentAge,
            ...$lifeData,
            'translations' => trans('view'),
        ];
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
