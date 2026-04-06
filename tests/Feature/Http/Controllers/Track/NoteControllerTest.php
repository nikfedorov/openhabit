<?php

declare(strict_types=1);

use App\Models\DailyNote;
use App\Models\User;

test('daily note can be created, updated, and deleted via empty content', function (): void {
    $user = User::factory()->create();
    $date = now()->toDateString();

    // Create
    $this->actingAs($user, 'sanctum')
        ->postJson('/api/track/daily-note', ['date' => $date, 'content' => 'Great day!'])
        ->assertNoContent();

    $this->assertDatabaseHas('daily_notes', [
        'user_id' => $user->id,
        'content' => 'Great day!',
    ]);

    // Update
    $this->actingAs($user, 'sanctum')
        ->postJson('/api/track/daily-note', ['date' => $date, 'content' => 'Updated content'])
        ->assertNoContent();

    $this->assertDatabaseHas('daily_notes', [
        'user_id' => $user->id,
        'content' => 'Updated content',
    ]);
    expect(DailyNote::query()->where('user_id', $user->id)->count())->toBe(1);

    // Delete via empty content
    $this->actingAs($user, 'sanctum')
        ->postJson('/api/track/daily-note', ['date' => $date, 'content' => ''])
        ->assertNoContent();

    expect(DailyNote::query()->where('user_id', $user->id)->count())->toBe(0);
});

test('daily note rejects content over 5000 chars', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user, 'sanctum')
        ->postJson('/api/track/daily-note', [
            'date' => now()->toDateString(),
            'content' => str_repeat('a', 5001),
        ])
        ->assertJsonValidationErrors(['content']);
});
