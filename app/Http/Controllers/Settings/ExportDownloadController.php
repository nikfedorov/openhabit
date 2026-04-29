<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Serve an export ZIP file via signed URL and clean up after download.
 */
final class ExportDownloadController
{
    public function __invoke(string $filename): BinaryFileResponse
    {
        $path = 'exports/'.$filename;
        $disk = Storage::disk('local');

        abort_unless($disk->exists($path), 404);

        $appSlug = Str::slug(config()->string('app.name'));
        $timestamp = now()->format('Ymd-His');
        $downloadName = sprintf('%s-export-%s.zip', $appSlug, $timestamp);

        app()->terminating(fn () => $disk->delete($path));

        return response()->download($disk->path($path), $downloadName);
    }
}
