<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Storage;

it('deletes old story images', function (): void {
    Storage::fake('public');
    Storage::disk('public')->put('stories/old-image.jpg', 'content');

    $this->travel(2)->hours();

    $this->artisan('app:clean-story-images')
        ->expectsOutputToContain('Deleted 1 story image(s)')
        ->assertExitCode(0);

    Storage::disk('public')->assertMissing('stories/old-image.jpg');
});

it('keeps recent story images', function (): void {
    Storage::fake('public');
    Storage::disk('public')->put('stories/new-image.jpg', 'content');

    $this->artisan('app:clean-story-images')
        ->expectsOutputToContain('Deleted 0 story image(s)')
        ->assertExitCode(0);

    Storage::disk('public')->assertExists('stories/new-image.jpg');
});

it('handles empty stories directory', function (): void {
    Storage::fake('public');

    $this->artisan('app:clean-story-images')
        ->expectsOutputToContain('Deleted 0 story image(s)')
        ->assertExitCode(0);
});
