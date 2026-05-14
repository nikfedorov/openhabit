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

it('flags telegram_bot_blocked_at and returns true when bot is blocked', function (): void {
    Log::spy();
    $user = User::factory()->telegram()->create(['telegram_bot_blocked_at' => null]);

    $result = (new FlagTelegramDeliveryFailure)->handle($user, new TelegramException('Forbidden: bot was blocked by the user', 403));

    expect($result)->toBeTrue();
    $user->refresh();
    expect($user->telegram_bot_blocked_at)->not->toBeNull();
});

it('flags telegram_user_deleted_at and returns true when user is deactivated', function (): void {
    Log::spy();
    $user = User::factory()->telegram()->create(['telegram_user_deleted_at' => null]);

    $result = (new FlagTelegramDeliveryFailure)->handle($user, new TelegramException('Forbidden: user is deactivated', 403));

    expect($result)->toBeTrue();
    $user->refresh();
    expect($user->telegram_user_deleted_at)->not->toBeNull();
});

it('flags telegram_user_deleted_at and returns true when chat not found', function (): void {
    Log::spy();
    $user = User::factory()->telegram()->create(['telegram_user_deleted_at' => null]);

    $result = (new FlagTelegramDeliveryFailure)->handle($user, new TelegramException('Bad Request: chat not found', 400));

    expect($result)->toBeTrue();
    $user->refresh();
    expect($user->telegram_user_deleted_at)->not->toBeNull();
});

it('returns false for unknown exceptions', function (): void {
    $user = User::factory()->telegram()->create();

    $result = (new FlagTelegramDeliveryFailure)->handle($user, new TelegramException('Too Many Requests: retry after 30', 429));

    expect($result)->toBeFalse();
});

it('returns true without touching db when user is null', function (): void {
    $result = (new FlagTelegramDeliveryFailure)->handle(null, new TelegramException('Forbidden: bot was blocked by the user', 403));

    expect($result)->toBeTrue()
        ->and(User::query()->whereNotNull('telegram_bot_blocked_at')->count())->toBe(0);
});

it('logs delivery failure with user id and reason', function (): void {
    Log::spy();
    $user = User::factory()->telegram()->create();

    (new FlagTelegramDeliveryFailure)->handle($user, new TelegramException('Forbidden: bot was blocked by the user', 403));

    Log::shouldHaveReceived('info')
        ->once()
        ->with('Telegram: delivery failure', [
            'user_id' => $user->id,
            'reason' => 'bot was blocked by the user',
        ]);
});
