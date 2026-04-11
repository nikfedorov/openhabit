<?php

declare(strict_types=1);

use App\Models\DailyNote;
use App\Models\User;

it('belongs to user', function (): void {
    $note = DailyNote::factory()->create();

    expect($note->user)->toBeInstanceOf(User::class);
});

it('has correct casts', function (): void {
    $note = DailyNote::factory()->create();

    expect($note->id)->toBeInt()
        ->and($note->user_id)->toBeString();
});
