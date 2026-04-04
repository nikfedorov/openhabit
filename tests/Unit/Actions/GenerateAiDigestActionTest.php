<?php

declare(strict_types=1);

use App\Actions\GenerateAiDigestAction;
use App\Models\AiDigest;
use App\Models\User;

test('returns existing digest for today', function (): void {
    $user = User::factory()->create();
    $digest = AiDigest::factory()->for($user)->create([
        'date' => now()->toDateString(),
    ]);

    $action = new GenerateAiDigestAction;
    $result = $action->execute($user);

    expect($result)->toBeInstanceOf(AiDigest::class)
        ->and($result->id)->toBe($digest->id);
});

test('returns null when no digest exists', function (): void {
    $user = User::factory()->create();

    $action = new GenerateAiDigestAction;
    $result = $action->execute($user);

    expect($result)->toBeNull();
});
