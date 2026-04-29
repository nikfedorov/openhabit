<?php

declare(strict_types=1);

use App\Jobs\SendTelegramErrorAlertJob;
use App\Logging\TelegramMonologHandler;
use Illuminate\Support\Facades\Queue;
use Monolog\Level;
use Monolog\LogRecord;

function makeTelegramRecord(string $message, Level $level = Level::Error, array $context = []): LogRecord
{
    return new LogRecord(
        datetime: new DateTimeImmutable,
        channel: 'test',
        level: $level,
        message: $message,
        context: $context,
    );
}

beforeEach(function (): void {
    Queue::fake();
});

test('respects configured log level', function (Level $level, bool $handlesError, bool $handlesWarning): void {
    $handler = new TelegramMonologHandler($level, '');

    expect($handler->getLevel())->toBe($level)
        ->and($handler->isHandling(makeTelegramRecord('msg', Level::Error)))->toBe($handlesError)
        ->and($handler->isHandling(makeTelegramRecord('msg', Level::Critical)))->toBeTrue()
        ->and($handler->isHandling(makeTelegramRecord('msg', Level::Warning)))->toBe($handlesWarning);
})->with([
    'error (default)' => [Level::Error, true, false],
    'critical' => [Level::Critical, false, false],
]);

test('dispatches job with formatted text when chatId is set', function (Level $level): void {
    $handler = new TelegramMonologHandler(Level::Debug, 'my-channel-id');

    $handler->handle(makeTelegramRecord('Something went wrong', $level, ['key' => 'value']));

    Queue::assertPushed(SendTelegramErrorAlertJob::class, fn (SendTelegramErrorAlertJob $job): bool => $job->chatId === 'my-channel-id'
        && str_contains($job->text, 'Something went wrong')
        && str_contains($job->text, $level->name)
        && str_contains($job->text, 'key')
        && str_contains($job->text, 'value'));
})->with([
    'error' => [Level::Error],
    'critical' => [Level::Critical],
    'warning' => [Level::Warning],
    'info' => [Level::Info],
]);

test('does not dispatch a job when chatId is empty', function (): void {
    $handler = new TelegramMonologHandler(Level::Error, '');

    $handler->handle(makeTelegramRecord('Something went wrong'));

    Queue::assertNothingPushed();
});

test('formats context values of various types', function (): void {
    $handler = new TelegramMonologHandler(Level::Debug, 'my-channel-id');

    $handler->handle(makeTelegramRecord('msg', Level::Info, [
        'flag_true' => true,
        'flag_false' => false,
        'nothing' => null,
        'nested' => ['a' => 1],
    ]));

    Queue::assertPushed(SendTelegramErrorAlertJob::class, fn (SendTelegramErrorAlertJob $job): bool => str_contains($job->text, 'true')
        && str_contains($job->text, 'false')
        && str_contains($job->text, 'null')
        && str_contains($job->text, 'nested')
        && str_contains($job->text, '&quot;a&quot;'));
});

test('telegram channel exists in logging config', function (): void {
    $channels = config('logging.channels');

    expect($channels)->toHaveKey('telegram')
        ->and($channels['telegram']['driver'])->toBe('monolog')
        ->and($channels['telegram']['handler'])->toBe(TelegramMonologHandler::class);
});
