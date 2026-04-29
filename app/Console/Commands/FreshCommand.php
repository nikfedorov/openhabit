<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

final class FreshCommand extends Command
{
    protected $signature = 'app:fresh';

    protected $description = 'Initialize the application (reset database and setup)';

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
