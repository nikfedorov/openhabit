<?php

declare(strict_types=1);

namespace App\Actions\View;

use App\Data\View\YearData;
use App\Models\User;
use App\Services\LifeGridService;
use App\Services\LifeYearCalculator;
use Carbon\CarbonInterface;

/**
 * Load year heatmap data for the View page.
 */
final readonly class YearAction
{
    public function __construct(
        private LifeGridService $lifeGridService,
        private LifeYearCalculator $lifeYearCalculator,
    ) {}

    public function handle(User $user, ?int $selectedYear = null): YearData
    {
        /** @var CarbonInterface|null $birthdate */
        $birthdate = $user->birthdate;

        $currentAge = $birthdate instanceof CarbonInterface
            ? $this->lifeYearCalculator->getCurrentAge($birthdate)
            : null;

        $effectiveYear = $selectedYear ?? $currentAge;

        $activityData = ($birthdate instanceof CarbonInterface && $effectiveYear !== null)
            ? $this->lifeGridService->getWeeklyActivityData($user, $birthdate, $effectiveYear)
            : null;

        return new YearData(
            selected: $effectiveYear,
            birthdate: $birthdate?->toDateString(),
            currentAge: $currentAge,
            activityData: $activityData,
        );
    }
}
