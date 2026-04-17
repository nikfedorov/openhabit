<?php

declare(strict_types=1);

use App\Models\User;

it('requires authentication', function (): void {
    $this->postJson('/api/settings/trial-banner/dismiss')
        ->assertUnauthorized();
});

it('sets trial_banner_dismissed_at to now', function (): void {
    $user = User::factory()->create(['trial_banner_dismissed_at' => null]);

    $this->actingAs($user, 'sanctum')
        ->postJson('/api/settings/trial-banner/dismiss')
        ->assertNoContent();

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'trial_banner_dismissed_at' => now(),
    ]);
});

it('does not extend the hidden period when banner was dismissed less than a day ago', function (): void {
    $dismissedAt = now()->subHours(12);

    $user = User::factory()->create(['trial_banner_dismissed_at' => $dismissedAt]);

    $this->actingAs($user, 'sanctum')
        ->postJson('/api/settings/trial-banner/dismiss')
        ->assertNoContent();

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'trial_banner_dismissed_at' => $dismissedAt,
    ]);
});

it('allows dismissing the banner again after one day has passed', function (): void {
    $dismissedAt = now()->subDay();

    $user = User::factory()->create(['trial_banner_dismissed_at' => $dismissedAt]);

    $this->actingAs($user, 'sanctum')
        ->postJson('/api/settings/trial-banner/dismiss')
        ->assertNoContent();

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'trial_banner_dismissed_at' => now(),
    ]);
});
