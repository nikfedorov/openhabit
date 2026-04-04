<?php

declare(strict_types=1);

use App\Models\Habit;
use App\Models\HabitNotification;
use App\Models\Setting;
use App\Models\User;

test('reports zero when no expired users', function (): void {
    User::factory()->premium()->create();

    $this->artisan('app:check-premium-expirations')
        ->expectsOutputToContain('Users downgraded: 0')
        ->assertExitCode(0);
});

test('clears ai_digest_time for expired users', function (): void {
    Setting::factory()->create(['key' => 'trial_period_days', 'value' => '0']);

    $user = User::factory()->create([
        'subscription_expires_at' => now()->subDay(),
        'ai_digest_time' => '09:00',
    ]);

    $this->artisan('app:check-premium-expirations')
        ->expectsOutputToContain('Users downgraded: 1')
        ->assertExitCode(0);

    expect($user->refresh()->ai_digest_time)->toBeNull();
});

test('deactivates extra notifications for expired users', function (): void {
    Setting::factory()->create(['key' => 'trial_period_days', 'value' => '0']);

    $user = User::factory()->create([
        'subscription_expires_at' => now()->subDay(),
    ]);
    $habit = Habit::factory()->for($user)->create();
    $notif1 = HabitNotification::factory()->for($habit)->create(['time' => '08:00', 'is_active' => true]);
    $notif2 = HabitNotification::factory()->for($habit)->create(['time' => '09:00', 'is_active' => true]);
    $notif3 = HabitNotification::factory()->for($habit)->create(['time' => '10:00', 'is_active' => true]);

    $this->artisan('app:check-premium-expirations')
        ->assertExitCode(0);

    expect($notif1->refresh()->is_active)->toBeTrue()
        ->and($notif2->refresh()->is_active)->toBeFalse()
        ->and($notif3->refresh()->is_active)->toBeFalse();
});

test('ignores users with active premium', function (): void {
    $user = User::factory()->premium()->create([
        'ai_digest_time' => '09:00',
    ]);

    $this->artisan('app:check-premium-expirations')
        ->expectsOutputToContain('Users downgraded: 0')
        ->assertExitCode(0);

    expect($user->refresh()->ai_digest_time)->toBe('09:00');
});
