<?php

declare(strict_types=1);

use App\Jobs\SendTelegramErrorAlertJob;
use Mockery\MockInterface;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Exceptions\TelegramException;

test('job configuration is correct', function (): void {
    $job = new SendTelegramErrorAlertJob('chat-id', 'hello');

    expect($job->queue)->toBe('notifications')
        ->and($job->tries)->toBe(2)
        ->and($job->timeout)->toBe(15);
});

test('handle sends message via nutgram', function (): void {
    $bot = $this->mock(Nutgram::class, function (MockInterface $mock): void {
        $mock->shouldReceive('sendMessage')
            ->once()
            ->withArgs(fn (string $text, mixed $chatId): bool => $text === 'hello' && $chatId === 'my-channel');
    });

    $job = new SendTelegramErrorAlertJob('my-channel', 'hello');
    $job->handle($bot);
});

test('handle skips when input is blank', function (string $chatId, string $text): void {
    $bot = $this->mock(Nutgram::class, function (MockInterface $mock): void {
        $mock->shouldNotReceive('sendMessage');
    });

    $job = new SendTelegramErrorAlertJob($chatId, $text);
    $job->handle($bot);
})->with([
    'empty chatId' => ['', 'hello'],
    'empty text' => ['chat-id', ''],
]);

test('handle swallows telegram exceptions silently', function (): void {
    $bot = $this->mock(Nutgram::class, function (MockInterface $mock): void {
        $mock->shouldReceive('sendMessage')
            ->once()
            ->andThrow(new TelegramException('Bad Request: chat not found'));
    });

    $job = new SendTelegramErrorAlertJob('bad-chat', 'hello');

    expect(fn () => $job->handle($bot))->not->toThrow(Throwable::class);
});
