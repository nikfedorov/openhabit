<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schedule;

// Send habit reminder notifications every 15 minutes
Schedule::command('app:send-notifications')->everyFifteenMinutes();

// Generate and send AI daily digests every 15 minutes
Schedule::command('app:send-ai-digests')->everyFifteenMinutes();

// Clean up old story images every hour
Schedule::command('app:clean-story-images')->hourly();

// Health-check free AI models every 15 minutes
Schedule::command('app:check-ai-models')->everyFifteenMinutes();

// Check for expired trials and subscriptions every hour
Schedule::command('app:check-premium-expirations')->hourly();
