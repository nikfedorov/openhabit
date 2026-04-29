<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

beforeEach(function (): void {
    Storage::fake('local');
});

it('allows download via signed url and deletes file after', function (): void {
    $filename = 'user-550e8400-e29b-41d4-a716-446655440000-20260404-120000.zip';

    Storage::disk('local')->put('exports/'.$filename, 'zip-content');

    $url = URL::signedRoute('export.download', ['filename' => $filename]);

    $response = $this->get($url);
    $response->assertSuccessful();
    $response->assertDownload();

    // Download name contains app slug, not user ID
    $contentDisposition = $response->headers->get('content-disposition');
    $appSlug = Str::slug(config()->string('app.name'));
    expect($contentDisposition)->toContain($appSlug);
    expect($contentDisposition)->not->toContain('550e8400');

    // File is deleted after send
    Storage::disk('local')->assertMissing('exports/'.$filename);
});

it('rejects unsigned requests and returns 404 for missing files', function (): void {
    $filename = 'user-550e8400-e29b-41d4-a716-446655440000-20260404-120000.zip';

    Storage::disk('local')->put('exports/'.$filename, 'zip-content');

    // Unsigned request returns 403
    $this->get(route('export.download', ['filename' => $filename]))
        ->assertForbidden();

    // Missing file with valid signature returns 404
    $missingFilename = 'user-660e8400-e29b-41d4-a716-446655440000-20260404-120000.zip';
    $url = URL::signedRoute('export.download', ['filename' => $missingFilename]);
    $this->get($url)->assertNotFound();

    // Invalid filename format returns 404 via route constraint
    $this->get('/export/bad-filename.zip')->assertNotFound();
});
