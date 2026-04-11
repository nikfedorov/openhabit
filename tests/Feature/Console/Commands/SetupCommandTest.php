<?php

declare(strict_types=1);

use App\Models\Setting;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Exceptions\TelegramException;
use SergiX44\Nutgram\Telegram\Types\User\User as TelegramUser;

it('sets up webhook and saves bot info', function (): void {
    $telegramUser = Mockery::mock(TelegramUser::class);
    $telegramUser->id = 12345;
    $telegramUser->username = 'test_bot';

    $bot = Mockery::mock(Nutgram::class);
    $bot->shouldReceive('setWebhook')->once();
    $bot->shouldReceive('getMe')->once()->andReturn($telegramUser);
    $this->app->instance(Nutgram::class, $bot);

    $this->artisan('app:setup')
        ->expectsOutputToContain('Telegram webhook successfully set')
        ->expectsOutputToContain('Bot info saved')
        ->assertExitCode(0);

    expect(Setting::getValue('telegram_bot_id'))->toBe('12345')
        ->and(Setting::getValue('telegram_bot_username'))->toBe('test_bot');
});

it('handles connect exception for webhook', function (): void {
    $bot = Mockery::mock(Nutgram::class);
    $bot->shouldReceive('setWebhook')
        ->andThrow(new ConnectException(
            'Connection refused',
            new Request('POST', 'test')
        ));
    $bot->shouldReceive('getMe')->andReturn(null);
    $this->app->instance(Nutgram::class, $bot);

    $this->artisan('app:setup')
        ->expectsOutputToContain('Failed to connect to Telegram API')
        ->assertExitCode(0);
});

it('handles telegram exception for webhook', function (): void {
    $bot = Mockery::mock(Nutgram::class);
    $bot->shouldReceive('setWebhook')
        ->andThrow(new TelegramException('Bad Request: webhook is already set'));
    $bot->shouldReceive('getMe')->andReturn(null);
    $this->app->instance(Nutgram::class, $bot);

    $this->artisan('app:setup')
        ->expectsOutputToContain('Telegram API error')
        ->assertExitCode(0);
});

it('handles connect exception for bot info', function (): void {
    $bot = Mockery::mock(Nutgram::class);
    $bot->shouldReceive('setWebhook')->once();
    $bot->shouldReceive('getMe')->once()->andThrow(new ConnectException(
        'Connection refused',
        new Request('POST', 'test')
    ));
    $this->app->instance(Nutgram::class, $bot);

    $this->artisan('app:setup')
        ->expectsOutputToContain('Failed to connect to Telegram API')
        ->assertExitCode(0);
});

it('handles telegram exception for bot info', function (): void {
    $bot = Mockery::mock(Nutgram::class);
    $bot->shouldReceive('setWebhook')->once();
    $bot->shouldReceive('getMe')->once()->andReturn(null);
    $this->app->instance(Nutgram::class, $bot);

    $this->artisan('app:setup')
        ->expectsOutputToContain('Failed to fetch bot information')
        ->assertExitCode(0);
});

it('includes secret token when safe mode enabled', function (): void {
    config(['nutgram.safe_mode' => true]);

    $telegramUser = Mockery::mock(TelegramUser::class);
    $telegramUser->id = 12345;
    $telegramUser->username = 'test_bot';

    $bot = Mockery::mock(Nutgram::class);
    $bot->shouldReceive('setWebhook')
        ->once()
        ->withArgs(fn (...$args): bool => $args[0] !== '' && ($args[6] ?? array_last($args) ?? null) !== null);
    $bot->shouldReceive('getMe')->once()->andReturn($telegramUser);
    $this->app->instance(Nutgram::class, $bot);

    $this->artisan('app:setup')->assertExitCode(0);
});
