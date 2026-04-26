<?php

declare(strict_types=1);

use App\Actions\UploadStoryImageAction;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

it('saves story image to public storage and returns its public url', function (): void {
    Storage::fake('public');

    $user = User::factory()->create();
    $url = resolve(UploadStoryImageAction::class)->handle($user, 'binary-png-data');

    Storage::disk('public')->assertExists(sprintf('stories/%d.png', $user->id));
    expect($url)->toContain(sprintf('stories/%d.png', $user->id))
        ->and(Storage::disk('public')->get(sprintf('stories/%d.png', $user->id)))->toBe('binary-png-data');
});
