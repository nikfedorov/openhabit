<?php

declare(strict_types=1);

use App\Actions\Telegram\FlagTelegramDeliveryFailure;
use App\Models\User;
use App\Telegram\Handlers\DeliveryFailureHandler;
use Illuminate\Support\Facades\Queue;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Exceptions\TelegramException;

beforeEach(function (): void {
    Queue::fake();
});

function deliveryFailureHandler(): DeliveryFailureHandler
{
    return new DeliveryFailureHandler(new FlagTelegramDeliveryFailure);
}

test('rethrows non-telegram exceptions untouched', function (): void {
    expect(fn () => (deliveryFailureHandler())(Mockery::mock(Nutgram::class), new RuntimeException('boom')))
        ->toThrow(RuntimeException::class, 'boom');
});

test('rethrows telegram exceptions that are not known delivery failures', function (): void {
    // Note: bot->userId() must NOT be called for unknown errors — the user
    // lookup is deferred until the action matches a known failure.
    $bot = Mockery::mock(Nutgram::class);
    $bot->shouldNotReceive('userId');

    expect(fn () => (deliveryFailureHandler())($bot, new TelegramException('Too Many Requests: retry after 30', 429)))
        ->toThrow(TelegramException::class, 'Too Many Requests');
});

test('resolves user by telegram_id and flags them on known delivery failure', function (): void {
    $user = User::factory()->telegram()->create(['telegram_bot_blocked_at' => null]);

    $bot = Mockery::mock(Nutgram::class);
    $bot->shouldReceive('userId')->andReturn((int) $user->telegram_id);

    (deliveryFailureHandler())($bot, new TelegramException('Forbidden: bot was blocked by the user', 403));

    expect($user->fresh()->telegram_bot_blocked_at)->not->toBeNull();
});

test('swallows known delivery failures even when userId is missing', function (): void {
    $bot = Mockery::mock(Nutgram::class);
    $bot->shouldReceive('userId')->andReturn(null);

    (deliveryFailureHandler())($bot, new TelegramException('Forbidden: bot was blocked by the user', 403));

    expect(User::query()->whereNotNull('telegram_bot_blocked_at')->count())->toBe(0);
});
