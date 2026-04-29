<?php

declare(strict_types=1);

use App\Actions\ResolvePremiumStateAction;
use App\Models\Setting;
use App\Models\User;

it('returns premium for users with an active subscription', function (): void {
    $user = User::factory()->premium()->create();

    $state = resolve(ResolvePremiumStateAction::class)->handle($user);

    expect($state->hasPremium)->toBeTrue()
        ->and($state->isTrialing)->toBeFalse()
        ->and($state->shouldShowBanner)->toBeFalse()
        ->and($state->trialRemaining)->toBeNull();
});

it('returns trial state when the trial is active without a subscription', function (): void {
    Setting::factory()->create(['key' => 'trial_period_days', 'value' => '14']);
    $user = User::factory()->create(['subscription_expires_at' => null]);

    $state = resolve(ResolvePremiumStateAction::class)->handle($user);

    expect($state->hasPremium)->toBeTrue()
        ->and($state->isTrialing)->toBeTrue()
        ->and($state->shouldShowBanner)->toBeTrue()
        ->and($state->trialRemaining)->toBeString()->not->toBeEmpty();
});

it('returns trial state when a subscription has expired but the trial is still active', function (): void {
    Setting::factory()->create(['key' => 'trial_period_days', 'value' => '14']);
    $user = User::factory()->create([
        'subscription_expires_at' => now()->subMinute(),
    ]);

    $state = resolve(ResolvePremiumStateAction::class)->handle($user);

    expect($state->hasPremium)->toBeTrue()
        ->and($state->isTrialing)->toBeTrue()
        ->and($state->shouldShowBanner)->toBeTrue()
        ->and($state->trialRemaining)->toBeString()->not->toBeEmpty();
});

it('hides the trial banner after dismissal', function (): void {
    Setting::factory()->create(['key' => 'trial_period_days', 'value' => '14']);
    $user = User::factory()->create([
        'subscription_expires_at' => null,
        'trial_banner_dismissed_at' => now(),
    ]);

    $state = resolve(ResolvePremiumStateAction::class)->handle($user);

    expect($state->hasPremium)->toBeTrue()
        ->and($state->isTrialing)->toBeTrue()
        ->and($state->shouldShowBanner)->toBeFalse();
});

it('shows the trial banner again after one day passes since dismissal', function (): void {
    Setting::factory()->create(['key' => 'trial_period_days', 'value' => '14']);
    $user = User::factory()->create([
        'subscription_expires_at' => null,
        'trial_banner_dismissed_at' => now()->subDay(),
    ]);

    $state = resolve(ResolvePremiumStateAction::class)->handle($user);

    expect($state->hasPremium)->toBeTrue()
        ->and($state->isTrialing)->toBeTrue()
        ->and($state->shouldShowBanner)->toBeTrue();
});

it('returns no premium when the trial has expired and there is no subscription', function (): void {
    Setting::factory()->create(['key' => 'trial_period_days', 'value' => '14']);
    $user = User::factory()->trialExpired()->create(['subscription_expires_at' => null]);

    $state = resolve(ResolvePremiumStateAction::class)->handle($user);

    expect($state->hasPremium)->toBeFalse()
        ->and($state->isTrialing)->toBeFalse()
        ->and($state->shouldShowBanner)->toBeFalse()
        ->and($state->trialRemaining)->toBeNull();
});

it('returns no trial remaining when the trial period is disabled', function (): void {
    Setting::factory()->create(['key' => 'trial_period_days', 'value' => '0']);
    $user = User::factory()->create(['subscription_expires_at' => null]);

    $state = resolve(ResolvePremiumStateAction::class)->handle($user);

    expect($state->hasPremium)->toBeFalse()
        ->and($state->isTrialing)->toBeFalse()
        ->and($state->shouldShowBanner)->toBeFalse()
        ->and($state->trialRemaining)->toBeNull();
});
