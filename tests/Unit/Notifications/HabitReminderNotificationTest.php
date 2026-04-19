<?php

declare(strict_types=1);

use App\Models\Habit;
use App\Notifications\Channels\TelegramChannel;
use App\Notifications\HabitReminderNotification;
use App\Notifications\Messages\TelegramMessage;
use Illuminate\Contracts\Queue\ShouldQueue;

test('is queued, routes via telegram, and builds message with name, description, and app button', function (): void {
    $habit = Habit::factory()->make([
        'id' => 1,
        'name' => 'Morning run',
        'description' => 'Run for 30 minutes',
    ]);
    $notification = new HabitReminderNotification($habit);

    expect($notification)
        ->toBeInstanceOf(ShouldQueue::class)
        ->and($notification->queue)->toBe('notifications')
        ->and($notification->via(new stdClass))->toBe([TelegramChannel::class])
        ->and($notification->habit->id)->toBe($habit->id);

    $message = $notification->toTelegram(new stdClass);

    expect($message)->toBeInstanceOf(TelegramMessage::class)
        ->and($message->getText())->toBe(__('telegram.reminder', ['name' => 'Morning run'])."\n".__('telegram.description', ['text' => 'Run for 30 minutes']));

    $rows = $message->getReplyMarkup()->jsonSerialize()['inline_keyboard'];
    expect($rows)->toHaveCount(1)
        ->and($rows[0][0]->text)->toBe(__('telegram.open_app'))
        ->and($rows[0][0]->web_app)->not->toBeNull();
});

test('toTelegram omits description line when null', function (): void {
    $habit = Habit::factory()->make([
        'id' => 2,
        'name' => 'Read',
        'description' => null,
    ]);

    $message = new HabitReminderNotification($habit)->toTelegram(new stdClass);

    expect($message->getText())->toBe(__('telegram.reminder', ['name' => 'Read']));
    expect($message->getReplyMarkup())->not->toBeNull();
});
