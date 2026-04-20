<?php

declare(strict_types=1);

use App\Jobs\SetTelegramMenuButtonJob;
use App\Models\AiTone;
use App\Models\CategoryTemplate;
use App\Models\HabitTemplate;
use App\Models\User;
use Illuminate\Support\Facades\Queue;

beforeEach(function (): void {
    Queue::fake();
});

it('sets ai_tone_id when null', function (): void {
    $tone = AiTone::factory()->create();
    $user = User::factory()->create(['ai_tone_id' => null]);

    expect($user->refresh()->ai_tone_id)->toBe($tone->id);
});

it('preserves existing ai_tone_id when creating event', function (): void {
    AiTone::factory()->create();
    $tone2 = AiTone::factory()->create();
    $user = User::factory()->create(['ai_tone_id' => $tone2->id]);

    expect($user->refresh()->ai_tone_id)->toBe($tone2->id);
});

it('applies templates to user when created', function (): void {
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

it('dispatches SetTelegramMenuButtonJob only for users with telegram_id', function (): void {
    $telegramUser = User::factory()->create(['telegram_id' => '12345']);
    User::factory()->create(['telegram_id' => null]);

    Queue::assertPushed(SetTelegramMenuButtonJob::class, 1);
    Queue::assertPushed(SetTelegramMenuButtonJob::class, fn (SetTelegramMenuButtonJob $job): bool => $job->userId === $telegramUser->id);
});
