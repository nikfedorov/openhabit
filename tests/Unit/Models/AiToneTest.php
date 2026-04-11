<?php

declare(strict_types=1);

use App\Models\AiTone;
use App\Models\User;

it('returns first active tone id on defaultId', function (): void {
    $tone = AiTone::factory()->create(['is_active' => true, 'sort_order' => 1]);
    AiTone::factory()->create(['is_active' => true, 'sort_order' => 2]);

    expect(AiTone::defaultId())->toBe($tone->id);
});

it('returns null when no active tones on defaultId', function (): void {
    AiTone::factory()->create(['is_active' => false]);

    expect(AiTone::defaultId())->toBeNull();
});

it('has many users', function (): void {
    $tone = AiTone::factory()->create();
    User::factory()->create(['ai_tone_id' => $tone->id]);

    expect($tone->users)->toHaveCount(1);
});

it('has correct casts', function (): void {
    $tone = AiTone::factory()->create();

    expect($tone->id)->toBeInt()
        ->and($tone->sort_order)->toBeInt()
        ->and($tone->is_active)->toBeBool();
});
