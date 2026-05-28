<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

#[Description('Delete story images older than one hour')]
#[Signature('app:clean-story-images')]
final class CleanStoryImagesCommand extends Command
{
    public function handle(): int
    {
        $disk = Storage::disk('public');
        $deleted = 0;

        /** @var array<int, string> $files */
        $files = $disk->files('stories');

        foreach ($files as $file) {
            if ($disk->lastModified($file) < now()->subHour()->getTimestamp()) {
                $disk->delete($file);
                $deleted++;
            }
        }

        $this->info(sprintf('Deleted %d story image(s).', $deleted));

        return self::SUCCESS;
    }
}
