<?php

declare(strict_types=1);

use App\Models\AiDigest;
use App\Models\User;

test('belongs to user', function (): void {
    $digest = AiDigest::factory()->create();

    expect($digest->user)->toBeInstanceOf(User::class);
});

test('casts are correct', function (): void {
    $digest = AiDigest::factory()->create();

    expect($digest->id)->toBeInt()
        ->and($digest->user_id)->toBeString();
});
