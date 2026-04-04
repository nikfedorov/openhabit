<?php

declare(strict_types=1);

use App\Models\AiTone;
use App\Models\CategoryTemplate;
use App\Models\HabitTemplate;
use App\Models\User;

test('creating event sets ai_tone_id when null', function (): void {
    $tone = AiTone::factory()->create();
    $user = User::factory()->create(['ai_tone_id' => null]);

    expect($user->refresh()->ai_tone_id)->toBe($tone->id);
});

test('creating event preserves existing ai_tone_id', function (): void {
    AiTone::factory()->create();
    $tone2 = AiTone::factory()->create();
    $user = User::factory()->create(['ai_tone_id' => $tone2->id]);

    expect($user->refresh()->ai_tone_id)->toBe($tone2->id);
});

test('created event applies templates to user', function (): void {
    $categoryTemplate = CategoryTemplate::factory()->create([
        'is_active' => true,
        'copy_by_default' => true,
    ]);
    HabitTemplate::factory()->for($categoryTemplate)->create([
        'is_active' => true,
        'copy_by_default' => true,
    ]);

    $user = User::factory()->create();

    expect($user->categories)->toHaveCount(1)
        ->and($user->habits)->toHaveCount(1);
});
