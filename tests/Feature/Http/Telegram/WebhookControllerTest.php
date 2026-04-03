<?php

declare(strict_types=1);

use SergiX44\Nutgram\Nutgram;

test('webhook endpoint processes telegram update', function (): void {
    $nutgram = Mockery::mock(Nutgram::class);
    $nutgram->shouldReceive('setRunningMode')->once();
    $nutgram->shouldReceive('run')->once();

    $this->app->instance(Nutgram::class, $nutgram);

    $this->postJson('/telegram/webhook', ['update_id' => 1])
        ->assertOk();
});
