<?php

declare(strict_types=1);

use App\Models\AiTone;
use App\Models\Setting;
use App\Models\User;

it('requires authentication', function (): void {
    $this->getJson('/api/settings')
        ->assertUnauthorized();
});

it('returns all user settings', function (): void {
    $tone = AiTone::factory()->create();
    $user = User::factory()->create([
        'theme' => 'dark',
        'locale' => 'ru',
        'timezone' => 'Europe/London',
        'day_starts_at' => '06:00:00',
        'move_completed_to_end' => false,
        'birthdate' => '1990-05-20',
        'ai_digest_time' => '08:30',
        'ai_tone_id' => $tone->id,
    ]);

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/settings')
        ->assertOk()
        ->assertJsonPath('data.theme', 'dark')
        ->assertJsonPath('data.moveCompletedToEnd', false)
        ->assertJsonPath('data.timezone', 'Europe/London')
        ->assertJsonPath('data.dayStartsAt', '06:00')
        ->assertJsonPath('data.birthdate', '1990-05-20')
        ->assertJsonPath('data.aiDigestTime', '08:30')
        ->assertJsonPath('data.aiToneId', $tone->id);
});

it('returns premium and trial state in the settings payload', function (): void {
    Setting::factory()->create(['key' => 'trial_period_days', 'value' => '14']);
    $user = User::factory()->create([
        'subscription_expires_at' => null,
        'trial_banner_dismissed_at' => null,
    ]);

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/settings')
        ->assertOk()
        ->assertJsonPath('hasPremium', true)
        ->assertJsonPath('data.trial.shouldShowBanner', true)
        ->assertJsonPath('data.trial.learnMore', __('app.learn_more'));
});
