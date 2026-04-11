<?php

declare(strict_types=1);

use App\Models\Stat;
use App\Models\User;
use App\Services\LifeGridService;
use App\Services\LifeYearCalculator;
use Illuminate\Support\Facades\Date;

beforeEach(function (): void {
    $this->service = resolve(LifeGridService::class);
});

it('returns nulls without birthdate', function (): void {
    $user = User::factory()->create(['birthdate' => null]);

    $result = $this->service->getLifeData($user, null, 0);

    expect($result)
        ->lifeStats->toBeNull()
        ->weeklyActivityData->toBeNull()
        ->yearlyActivityData->toBeNull();
});

it('returns 52 weeks and 80 years of data with stats', function (): void {
    $birthdate = Date::parse('2000-01-15');
    $user = User::factory()->create(['birthdate' => $birthdate->toDateString()]);
    $yearStart = resolve(LifeYearCalculator::class)->getYearStart($birthdate, 0);

    Stat::factory()->weekly()->create([
        'user_id' => $user->id,
        'period_start' => $yearStart->toDateString(),
        'completed_count' => 10,
        'planned_count' => 10,
    ]);

    $result = $this->service->getLifeData($user, $birthdate, 0);

    expect($result['weeklyActivityData'])->toHaveCount(52)
        ->and($result['weeklyActivityData'][0])->toHaveKeys(['weekNum', 'intensity', 'completed', 'total'])
        ->and($result['weeklyActivityData'][0]['intensity'])->toBe(4)
        ->and($result['weeklyActivityData'][0]['completed'])->toBe(10)
        ->and($result['yearlyActivityData'])->toHaveCount(LifeGridService::TOTAL_LIFE_YEARS)
        ->and($result['yearlyActivityData'][0])->toHaveKeys(['year', 'intensity', 'completed', 'total'])
        ->and($result['yearlyActivityData'][0]['completed'])->toBe(10)
        ->and($result['yearlyActivityData'][0]['total'])->toBe(10);
});

it('returns null weeklyActivityData without selectedYear', function (): void {
    $birthdate = Date::parse('1990-01-01');
    $user = User::factory()->create(['birthdate' => $birthdate->toDateString()]);

    $result = $this->service->getLifeData($user, $birthdate, null);

    expect($result['weeklyActivityData'])->toBeNull()
        ->and($result['yearlyActivityData'])->toHaveCount(LifeGridService::TOTAL_LIFE_YEARS);
});

it('returns correct structure for life stats', function (): void {
    $birthdate = Date::parse('2000-01-15');

    $result = $this->service->getLifeStats($birthdate);

    expect($result)->toHaveKeys(['currentAge', 'weeksLived', 'yearsRemaining'])
        ->and($result['currentAge'])->toBeInt()
        ->and($result['yearsRemaining'])->toBe(LifeGridService::TOTAL_LIFE_YEARS - $result['currentAge']);
});
