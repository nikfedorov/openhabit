<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schedule;

// Send habit reminder notifications every 5 minutes
Schedule::command('app:send-notifications')->everyFiveMinutes();

// Generate and send AI daily digests every 5 minutes
Schedule::command('app:send-ai-digests')->everyFiveMinutes();

// Clean up old story images every hour
Schedule::command('app:clean-story-images')->hourly();

// Check for expired trials and subscriptions every hour
Schedule::command('app:check-premium-expirations')->hourly();

// Clean up old export files daily
Schedule::command('app:clean-export-files')->daily();
