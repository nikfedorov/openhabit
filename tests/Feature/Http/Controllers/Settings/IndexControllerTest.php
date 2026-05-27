<?php

declare(strict_types=1);

use App\Models\AiTone;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;

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
        ->assertJson(fn (AssertableJson $json): AssertableJson => $json
            ->has('data', fn (AssertableJson $json): AssertableJson => $json
                ->where('theme', 'dark')
                ->where('locale', 'ru')
                ->where('timezone', 'Europe/London')
                ->where('dayStartsAt', '06:00')
                ->where('moveCompletedToEnd', false)
                ->where('birthdate', '1990-05-20')
                ->where('aiDigestTime', '08:30')
                ->where('aiToneId', $tone->id)
                ->where('longTermGoal', null)
                ->etc()
            )
            ->etc()
        );
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
