<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Storage;

it('rejects unauthenticated requests', function (): void {
    $this->postJson('/api/story/upload')
        ->assertUnauthorized();
});

it('rejects invalid image data', function (array $payload, string $errorField): void {
    $user = User::factory()->create();

    $this->actingAs($user, 'sanctum')
        ->postJson('/api/story/upload', $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors($errorField);
})->with([
    'missing image' => [[], 'image'],
    'non-string image' => [['image' => 123], 'image'],
    'invalid format' => [['image' => 'not-a-valid-data-url'], 'image'],
    'invalid base64' => [['image' => 'data:image/png;base64,!!!invalid!!!'], 'image'],
]);

it('uploads story image and returns url', function (): void {
    Storage::fake('public');

    $user = User::factory()->create();

    // 1x1 transparent PNG
    $pngBase64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';
    $dataUrl = 'data:image/png;base64,'.$pngBase64;

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/story/upload', ['image' => $dataUrl])
        ->assertSuccessful()
        ->assertJsonStructure(['url']);

    Storage::disk('public')->assertExists(sprintf('stories/%d.png', $user->id));

    expect($response->json('url'))->toBeString()->toContain(sprintf('stories/%d.png', $user->id));
});
