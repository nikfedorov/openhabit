<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

final class CleanStoryImagesCommand extends Command
{
    protected $signature = 'app:clean-story-images';

    protected $description = 'Delete story images older than one hour';

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
