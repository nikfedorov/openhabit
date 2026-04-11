<?php

declare(strict_types=1);

use SergiX44\Nutgram\Nutgram;

it('calls setup and generate-invoice-links', function (): void {
    $bot = Mockery::mock(Nutgram::class);
    $bot->shouldReceive('setWebhook')->once();
    $bot->shouldReceive('getMe')->once()->andReturn(null);
    $this->app->instance(Nutgram::class, $bot);

    $this->artisan('app:fresh')
        ->expectsOutputToContain('Application initialized successfully!')
        ->assertExitCode(0);
});
