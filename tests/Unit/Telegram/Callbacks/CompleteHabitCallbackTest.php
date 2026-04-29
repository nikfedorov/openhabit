<?php

declare(strict_types=1);

use App\Models\Habit;
use App\Models\HabitCompletion;
use App\Models\User;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\User\User as TelegramUser;
use SergiX44\Nutgram\Testing\FakeNutgram;

it('marks a habit as completed and updates the keyboard', function (): void {
    $user = User::factory()->telegram()->create([
        'telegram_id' => '12345',
        'timezone' => 'UTC',
        'locale' => 'en',
    ]);
    $habit = Habit::factory()->create(['user_id' => $user->id]);

    /** @var FakeNutgram $bot */
    $bot = resolve(Nutgram::class);

    $telegramUser = TelegramUser::make(
        id: 12345,
        is_bot: false,
        first_name: 'Test',
    );

    $bot->setCommonUser($telegramUser)
        ->hearCallbackQueryData('complete_habit:'.$habit->id)
        ->reply()
        ->assertSequence(
            fn ($bot) => $bot->assertReply('editMessageReplyMarkup'),
            fn ($bot) => $bot->assertReply('answerCallbackQuery'),
        );

    expect(HabitCompletion::query()->where('habit_id', $habit->id)->count())->toBe(1);
});

it('returns already completed message when habit is done today', function (): void {
    $user = User::factory()->telegram()->create([
        'telegram_id' => '12345',
        'timezone' => 'UTC',
        'locale' => 'en',
    ]);
    $habit = Habit::factory()->create(['user_id' => $user->id]);
    HabitCompletion::factory()->create([
        'habit_id' => $habit->id,
        'user_id' => $user->id,
        'completed_at' => now()->toDateString(),
        'current_iteration' => 1,
    ]);

    /** @var FakeNutgram $bot */
    $bot = resolve(Nutgram::class);

    $telegramUser = TelegramUser::make(
        id: 12345,
        is_bot: false,
        first_name: 'Test',
    );

    $bot->setCommonUser($telegramUser)
        ->hearCallbackQueryData('complete_habit:'.$habit->id)
        ->reply()
        ->assertReply('answerCallbackQuery');

    expect(HabitCompletion::query()->where('habit_id', $habit->id)->count())->toBe(1);
});

it('returns habit not found when habit belongs to another user', function (): void {
    $otherUser = User::factory()->telegram()->create([
        'telegram_id' => '99999',
    ]);
    $habit = Habit::factory()->create(['user_id' => $otherUser->id]);

    /** @var FakeNutgram $bot */
    $bot = resolve(Nutgram::class);

    $telegramUser = TelegramUser::make(
        id: 12345,
        is_bot: false,
        first_name: 'Test',
    );

    $bot->setCommonUser($telegramUser)
        ->hearCallbackQueryData('complete_habit:'.$habit->id)
        ->reply()
        ->assertReply('answerCallbackQuery');

    expect(HabitCompletion::query()->count())->toBe(0);
});
