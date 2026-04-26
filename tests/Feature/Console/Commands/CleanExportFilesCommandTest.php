<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Storage;

it('deletes old export files and keeps recent ones', function (): void {
    Storage::fake('local');

    // Works with empty directory
    $this->artisan('app:clean-export-files')
        ->expectsOutput('Deleted 0 export file(s).')
        ->assertSuccessful();

    // Create an old file and a recent file
    Storage::disk('local')->put('exports/old.zip', 'old-content');
    touch(Storage::disk('local')->path('exports/old.zip'), now()->subDays(2)->getTimestamp());

    Storage::disk('local')->put('exports/recent.zip', 'recent-content');

    // Deletes only old files
    $this->artisan('app:clean-export-files')
        ->expectsOutput('Deleted 1 export file(s).')
        ->assertSuccessful();

    Storage::disk('local')->assertMissing('exports/old.zip');
    Storage::disk('local')->assertExists('exports/recent.zip');
});
