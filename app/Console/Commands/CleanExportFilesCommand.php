<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * Delete export files older than one day.
 */
final class CleanExportFilesCommand extends Command
{
    protected $signature = 'app:clean-export-files';

    protected $description = 'Delete export files older than one day';

    public function handle(): int
    {
        $disk = Storage::disk('local');
        $deleted = 0;

        /** @var array<int, string> $files */
        $files = $disk->files('exports');

        foreach ($files as $file) {
            if ($disk->lastModified($file) < now()->subDay()->getTimestamp()) {
                $disk->delete($file);
                $deleted++;
            }
        }

        $this->info(sprintf('Deleted %d export file(s).', $deleted));

        return self::SUCCESS;
    }
}
