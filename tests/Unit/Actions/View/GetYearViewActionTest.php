<?php

declare(strict_types=1);

use App\Actions\View\GetYearViewAction;
use App\Models\User;

it('returns year data using current age when no year is provided', function (): void {
    $user = User::factory()->create(['birthdate' => '1990-01-15']);

    $data = resolve(GetYearViewAction::class)->handle($user);

    expect($data->birthdate)->toBe('1990-01-15')
        ->and($data->currentAge)->toBeInt()
        ->and($data->selected)->toBe($data->currentAge)
        ->and($data->activityData)->not->toBeNull();
});

it('uses the explicitly selected year when provided', function (): void {
    $user = User::factory()->create(['birthdate' => '1990-01-15']);

    $data = resolve(GetYearViewAction::class)->handle($user, 5);

    expect($data->selected)->toBe(5)
        ->and($data->activityData)->not->toBeNull();
});

it('returns nulls when user has no birthdate', function (): void {
    $user = User::factory()->create(['birthdate' => null]);

    $data = resolve(GetYearViewAction::class)->handle($user);

    expect($data->birthdate)->toBeNull()
        ->and($data->currentAge)->toBeNull()
        ->and($data->selected)->toBeNull()
        ->and($data->activityData)->toBeNull();
});
