<?php

declare(strict_types=1);

use App\Notifications\Messages\TelegramMessage;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

test('text and line methods build message content', function (): void {
    // text() sets and overwrites
    $message = TelegramMessage::create();
    expect($message->getText())->toBe('');

    $message->text('First');
    expect($message->getText())->toBe('First');

    $message->text('Second');
    expect($message->getText())->toBe('Second');

    // line() appends with newline separator
    $message = TelegramMessage::create()
        ->line('First line')
        ->line('Second line');

    expect($message->getText())->toBe("First line\nSecond line");

    // line() on empty message does not prepend newline
    expect(TelegramMessage::create()->line('Only line')->getText())->toBe('Only line');
});

test('all builder methods are fluent', function (): void {
    $message = TelegramMessage::create();

    expect($message)->toBeInstanceOf(TelegramMessage::class)
        ->and($message->text('test'))->toBe($message)
        ->and($message->line('test'))->toBe($message)
        ->and($message->button('Click', 'data'))->toBe($message)
        ->and($message->webAppButton('Open', 'https://example.com'))->toBe($message);
});

test('button and webAppButton build inline keyboard', function (): void {
    // No buttons — no markup
    expect(TelegramMessage::create()->text('No buttons')->getReplyMarkup())->toBeNull();

    // Callback button
    $message = TelegramMessage::create()->button('✅ Done', 'complete:1');
    $markup = $message->getReplyMarkup();
    expect($markup)->toBeInstanceOf(InlineKeyboardMarkup::class);

    $rows = $markup->jsonSerialize()['inline_keyboard'];
    expect($rows)->toHaveCount(1)
        ->and($rows[0][0]->text)->toBe('✅ Done')
        ->and($rows[0][0]->callback_data)->toBe('complete:1');

    // WebApp button
    $message = TelegramMessage::create()->webAppButton('🚀 Open', 'https://example.com');
    $rows = $message->getReplyMarkup()->jsonSerialize()['inline_keyboard'];
    expect($rows)->toHaveCount(1)
        ->and($rows[0][0]->text)->toBe('🚀 Open')
        ->and($rows[0][0]->web_app)->not->toBeNull();

    // Multiple buttons create separate rows
    $message = TelegramMessage::create()
        ->button('Button 1', 'data1')
        ->button('Button 2', 'data2');

    expect($message->getReplyMarkup()->jsonSerialize()['inline_keyboard'])->toHaveCount(2);
});
