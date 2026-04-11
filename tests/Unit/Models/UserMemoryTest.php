<?php

declare(strict_types=1);

use App\Enums\MemoryCategory;
use App\Models\User;
use App\Models\UserMemory;

it('belongs to user', function (): void {
    $memory = UserMemory::factory()->create();

    expect($memory->user)->toBeInstanceOf(User::class);
});

it('has correct casts', function (): void {
    $memory = UserMemory::factory()->create();

    expect($memory->id)->toBeInt()
        ->and($memory->user_id)->toBeString()
        ->and($memory->category)->toBeInstanceOf(MemoryCategory::class);
});
