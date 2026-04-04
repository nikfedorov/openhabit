<?php

declare(strict_types=1);

use App\Models\AiLog;
use App\Models\User;

test('belongs to user', function (): void {
    $log = AiLog::factory()->create();

    expect($log->user)->toBeInstanceOf(User::class);
});

test('casts are correct', function (): void {
    $log = AiLog::factory()->create();

    expect($log->id)->toBeInt()
        ->and($log->user_id)->toBeString()
        ->and($log->is_successful)->toBeBool();
});
