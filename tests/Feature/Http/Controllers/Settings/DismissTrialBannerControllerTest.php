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

it('is idempotent when banner already dismissed', function (): void {
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
