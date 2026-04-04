<?php

declare(strict_types=1);

use App\Models\DailyNote;
use App\Models\User;

test('belongs to user', function (): void {
    $note = DailyNote::factory()->create();

    expect($note->user)->toBeInstanceOf(User::class);
});

test('casts are correct', function (): void {
    $note = DailyNote::factory()->create();

    expect($note->id)->toBeInt()
        ->and($note->user_id)->toBeString();
});
