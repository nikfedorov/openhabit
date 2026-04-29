<?php

declare(strict_types=1);

use App\Models\AiDigest;
use App\Models\User;

it('belongs to user', function (): void {
    $digest = AiDigest::factory()->create();

    expect($digest->user)->toBeInstanceOf(User::class);
});

it('has correct casts', function (): void {
    $digest = AiDigest::factory()->create();

    expect($digest->id)->toBeInt()
        ->and($digest->user_id)->toBeInt();
});
