<?php

declare(strict_types=1);

use App\Notifications\AiDigestNotification;
use App\Notifications\Channels\TelegramChannel;
use App\Notifications\Messages\TelegramMessage;
use Illuminate\Contracts\Queue\ShouldQueue;

test('is queued, routes via telegram, and builds message with digest content and app button', function (): void {
    $notification = new AiDigestNotification('Great work today! You completed 5 habits.', 'April 19, 2026, Sunday');

    expect($notification)
        ->toBeInstanceOf(ShouldQueue::class)
        ->and($notification->queue)->toBe('notifications')
        ->and($notification->via(new stdClass))->toBe([TelegramChannel::class]);

    $message = $notification->toTelegram(new stdClass);

    expect($message)->toBeInstanceOf(TelegramMessage::class)
        ->and($message->getText())->toContain('<b>April 19, 2026, Sunday</b>')
        ->and($message->getText())->toContain('Great work today! You completed 5 habits.')
        ->and($message->getParseMode())->toBe('HTML');

    $rows = $message->getReplyMarkup()->jsonSerialize()['inline_keyboard'];

    expect($rows)->toHaveCount(1)
        ->and($rows[0][0]->text)->toBe(__('telegram.open_app_plain'))
        ->and($rows[0][0]->web_app)->not->toBeNull();
});

test('accepts null content', function (): void {
    $notification = new AiDigestNotification(null, 'April 19, 2026, Sunday');

    expect($notification->content)->toBeNull();

    $message = $notification->toTelegram(new stdClass);

    expect($message)->toBeInstanceOf(TelegramMessage::class);
});
