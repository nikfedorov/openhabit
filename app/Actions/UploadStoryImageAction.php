<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Facades\Storage;

/**
 * Saves a story image to public storage and returns its URL.
 */
final readonly class UploadStoryImageAction
{
    /**
     * @param  string  $imageData  Raw PNG binary data
     * @return string Public URL of the stored image
     */
    public function handle(User $user, string $imageData): string
    {
        $filename = sprintf('stories/%d.png', $user->id);

        Storage::disk('public')->put($filename, $imageData);

        return Storage::disk('public')->url($filename);
    }
}
