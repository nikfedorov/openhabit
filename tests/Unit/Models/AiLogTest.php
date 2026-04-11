<?php

declare(strict_types=1);

use App\Models\AiLog;
use App\Models\User;

it('belongs to user', function (): void {
    $log = AiLog::factory()->create();

    expect($log->user)->toBeInstanceOf(User::class);
});

it('has correct casts', function (): void {
    $log = AiLog::factory()->create();

    expect($log->id)->toBeInt()
        ->and($log->user_id)->toBeString()
        ->and($log->is_successful)->toBeBool();
});
