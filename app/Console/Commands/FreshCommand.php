<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Description('Initialize the application (reset database and setup)')]
#[Signature('app:fresh')]
final class FreshCommand extends Command
{
    public function handle(): int
    {
        $this->runMigrations();
        $this->call('app:setup');
        $this->call('app:generate-invoice-links');

        $this->info('Application initialized successfully!');

        return 0;
    }

    /**
     * @codeCoverageIgnore
     */
    private function runMigrations(): void
    {
        if (app()->runningUnitTests()) {
            return;
        }

        $this->call('migrate:fresh', ['--seed' => true]);
    }
}
