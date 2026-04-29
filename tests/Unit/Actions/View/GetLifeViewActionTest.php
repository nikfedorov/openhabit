<?php

declare(strict_types=1);

use App\Actions\View\GetLifeViewAction;
use App\Models\User;
use App\Services\LifeGridService;

it('returns life data with activity when user has a birthdate', function (): void {
    $user = User::factory()->create(['birthdate' => '1990-01-15']);

    $data = resolve(GetLifeViewAction::class)->handle($user);

    expect($data->birthdate)->toBe('1990-01-15')
        ->and($data->currentAge)->toBeInt()
        ->and($data->weeksLived)->toBeInt()
        ->and($data->yearsRemaining)->toBeInt()
        ->and($data->activityData)->toHaveCount(LifeGridService::TOTAL_LIFE_YEARS);
});

it('returns nulls when user has no birthdate', function (): void {
    $user = User::factory()->create(['birthdate' => null]);

    $data = resolve(GetLifeViewAction::class)->handle($user);

    expect($data->birthdate)->toBeNull()
        ->and($data->currentAge)->toBeNull()
        ->and($data->weeksLived)->toBeNull()
        ->and($data->yearsRemaining)->toBeNull()
        ->and($data->activityData)->toBeNull();
});
