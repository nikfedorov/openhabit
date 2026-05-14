<?php

declare(strict_types=1);

use App\Models\User;
use App\Telegram\Handlers\DeliveryFailureHandler;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Mockery\MockInterface;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Exceptions\TelegramException;

beforeEach(function (): void {
    Queue::fake();
});

/**
 * @return MockInterface&Nutgram
 */
function mockBotWithUserId(string $telegramId): MockInterface
{
    $bot = Mockery::mock(Nutgram::class);
    $bot->shouldReceive('userId')->andReturn((int) $telegramId);

    return $bot;
}

test('flags telegram_bot_blocked_at when bot is blocked by user', function (): void {
    Log::spy();
    $user = User::factory()->telegram()->create(['telegram_bot_blocked_at' => null]);

    $bot = mockBotWithUserId($user->telegram_id);

    (new DeliveryFailureHandler)($bot, new TelegramException('Forbidden: bot was blocked by the user', 403));

    $user->refresh();
    expect($user->telegram_bot_blocked_at)->not->toBeNull();
});

test('flags telegram_user_deleted_at when user is deactivated', function (): void {
    Log::spy();
    $user = User::factory()->telegram()->create(['telegram_user_deleted_at' => null]);

    $bot = mockBotWithUserId($user->telegram_id);

    (new DeliveryFailureHandler)($bot, new TelegramException('Forbidden: user is deactivated', 403));

    $user->refresh();
    expect($user->telegram_user_deleted_at)->not->toBeNull();
});

test('flags telegram_user_deleted_at when chat not found', function (): void {
    Log::spy();
    $user = User::factory()->telegram()->create(['telegram_user_deleted_at' => null]);

    $bot = mockBotWithUserId($user->telegram_id);

    (new DeliveryFailureHandler)($bot, new TelegramException('Bad Request: chat not found', 400));

    $user->refresh();
    expect($user->telegram_user_deleted_at)->not->toBeNull();
});

test('rethrows unrelated telegram exceptions', function (): void {
    $bot = Mockery::mock(Nutgram::class);
    $bot->shouldReceive('userId')->andReturn(12345);

    expect(fn () => (new DeliveryFailureHandler)($bot, new TelegramException('Too Many Requests: retry after 30', 429)))
        ->toThrow(TelegramException::class, 'Too Many Requests');
});

test('rethrows non-telegram exceptions', function (): void {
    $bot = Mockery::mock(Nutgram::class);

    expect(fn () => (new DeliveryFailureHandler)($bot, new RuntimeException('Something went wrong')))
        ->toThrow(RuntimeException::class, 'Something went wrong');
});

test('does nothing when userId returns empty', function (): void {
    Log::spy();
    $bot = Mockery::mock(Nutgram::class);
    $bot->shouldReceive('userId')->andReturn(0);

    // Should not throw and not touch the database
    (new DeliveryFailureHandler)($bot, new TelegramException('Forbidden: bot was blocked by the user', 403));

    expect(User::query()->whereNotNull('telegram_bot_blocked_at')->count())->toBe(0);
});

test('logs delivery failure with telegram id and reason', function (): void {
    Log::spy();
    $user = User::factory()->telegram()->create();

    $bot = mockBotWithUserId($user->telegram_id);

    (new DeliveryFailureHandler)($bot, new TelegramException('Forbidden: bot was blocked by the user', 403));

    Log::shouldHaveReceived('info')
        ->once()
        ->with('DeliveryFailureHandler: delivery failure', [
            'telegram_id' => $user->telegram_id,
            'reason' => 'bot was blocked by the user',
        ]);
});
