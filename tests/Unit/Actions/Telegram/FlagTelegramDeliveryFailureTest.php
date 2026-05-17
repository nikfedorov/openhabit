<?php

declare(strict_types=1);

use App\Actions\Telegram\FlagTelegramDeliveryFailure;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use SergiX44\Nutgram\Telegram\Exceptions\TelegramException;

beforeEach(function (): void {
    Queue::fake();
});

it('flags the matching user column for each known delivery failure', function (string $message, string $column): void {
    $user = User::factory()->telegram()->create([$column => null]);

    $result = (new FlagTelegramDeliveryFailure)->handle(
        new TelegramException($message, 400),
        fn (): User => $user,
    );

    expect($result)->toBeTrue()
        ->and($user->fresh()->{$column})->not->toBeNull();
})->with([
    'bot blocked' => ['Forbidden: bot was blocked by the user', 'telegram_bot_blocked_at'],
    'user deactivated' => ['Forbidden: user is deactivated', 'telegram_user_deleted_at'],
    'chat not found' => ['Bad Request: chat not found', 'telegram_user_deleted_at'],
]);

it('returns false and skips user resolution for unknown exceptions', function (): void {
    $resolverCalled = false;

    $result = (new FlagTelegramDeliveryFailure)->handle(
        new TelegramException('Too Many Requests: retry after 30', 429),
        function () use (&$resolverCalled): ?User {
            $resolverCalled = true;

            return null;
        },
    );

    expect($result)->toBeFalse()
        ->and($resolverCalled)->toBeFalse();
});

it('returns true without touching the database when resolver yields null', function (): void {
    $result = (new FlagTelegramDeliveryFailure)->handle(
        new TelegramException('Forbidden: bot was blocked by the user', 403),
        fn (): ?User => null,
    );

    expect($result)->toBeTrue()
        ->and(User::query()->whereNotNull('telegram_bot_blocked_at')->count())->toBe(0);
});

it('logs the failure with user id and reason', function (): void {
    Log::spy();

    $user = User::factory()->telegram()->create();

    (new FlagTelegramDeliveryFailure)->handle(
        new TelegramException('Forbidden: bot was blocked by the user', 403),
        fn (): User => $user,
    );

    Log::shouldHaveReceived('info')
        ->once()
        ->with('Telegram: delivery failure', [
            'user_id' => $user->id,
            'reason' => 'bot was blocked by the user',
        ]);
});
