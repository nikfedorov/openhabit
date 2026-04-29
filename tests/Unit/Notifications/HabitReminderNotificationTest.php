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
        ->and($message->getParseMode())->toBe('HTML')
        ->and($message->getText())->toBe(
            '<b>'.__('telegram.reminder_header').'</b>'."\n".'Morning run'."\n"."\n".__('telegram.description', ['text' => 'Run for 30 minutes'])
        );

    $rows = $message->getReplyMarkup()->jsonSerialize()['inline_keyboard'];
    expect($rows)->toHaveCount(1)
        ->and($rows[0][0]->text)->toBe(__('telegram.mark_as_done'))
        ->and($rows[0][0]->callback_data)->toBe('complete_habit:1')
        ->and($rows[0][1]->text)->toBe(__('telegram.open_app'))
        ->and($rows[0][1]->web_app)->not->toBeNull();
});

test('toTelegram omits description line when null', function (): void {
    $habit = Habit::factory()->make([
        'id' => 2,
        'name' => 'Read',
        'description' => null,
    ]);

    $message = new HabitReminderNotification($habit)->toTelegram(new stdClass);

    expect($message->getText())->toBe('<b>'.__('telegram.reminder_header').'</b>'."\n".'Read');
    expect($message->getParseMode())->toBe('HTML');
    expect($message->getReplyMarkup())->not->toBeNull();
});
