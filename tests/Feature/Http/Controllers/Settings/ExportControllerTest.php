<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Storage;

beforeEach(function (): void {
    Storage::fake('local');
});

it('exports data for premium user and returns signed url', function (): void {
    $user = User::factory()->premium()->create();

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/settings/export');

    $response->assertSuccessful();
    $response->assertJsonStructure(['url']);

    $url = $response->json('url');
    expect($url)->toContain('/export/user-');
    expect($url)->toContain('signature=');
});

it('rejects export for non-premium users and requires authentication', function (): void {
    $this->postJson('/api/settings/export')
        ->assertUnauthorized();

    $user = User::factory()->create([
        'subscription_expires_at' => null,
        'created_at' => now()->subDays(30),
    ]);

    $this->actingAs($user, 'sanctum')
        ->postJson('/api/settings/export')
        ->assertForbidden();
});
