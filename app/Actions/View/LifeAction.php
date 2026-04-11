<?php

declare(strict_types=1);

namespace App\Actions\View;

use App\Data\View\LifeData;
use App\Models\User;
use App\Services\LifeGridService;
use Carbon\CarbonInterface;

/**
 * Load memento mori life grid data for the View page.
 */
final readonly class LifeAction
{
    public function __construct(
        private LifeGridService $lifeGridService,
    ) {}

    public function handle(User $user): LifeData
    {
        /** @var CarbonInterface|null $birthdate */
        $birthdate = $user->birthdate;

        if (! $birthdate instanceof CarbonInterface) {
            return new LifeData(
                birthdate: null,
                currentAge: null,
                weeksLived: null,
                yearsRemaining: null,
                activityData: null,
            );
        }

        $lifeData = $this->lifeGridService->getLifeData($user, $birthdate, selectedYear: null);
        $lifeStats = $lifeData['lifeStats'];

        return new LifeData(
            birthdate: $birthdate->toDateString(),
            currentAge: $lifeStats?->currentAge,
            weeksLived: $lifeStats?->weeksLived,
            yearsRemaining: $lifeStats?->yearsRemaining,
            activityData: $lifeData['yearlyActivityData'],
        );
    }
}
