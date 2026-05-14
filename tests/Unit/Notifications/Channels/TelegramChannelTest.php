<?php

declare(strict_types=1);

use App\Actions\Telegram\FlagTelegramDeliveryFailure;
use App\Models\User;
use App\Notifications\Channels\TelegramChannel;
use App\Notifications\Contracts\SendsTelegramNotification;
use App\Notifications\Messages\TelegramMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Queue;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Exceptions\TelegramException;

beforeEach(function (): void {
    Queue::fake();
});

test('send delivers message via nutgram bot', function (): void {
    $user = User::factory()->telegramId()->create();

    $notification = Mockery::mock(Notification::class, SendsTelegramNotification::class);
    $notification->shouldReceive('toTelegram')
        ->with($user)
        ->andReturn(TelegramMessage::create()->text('Test message'));

    $bot = Mockery::mock(Nutgram::class);
    $bot->shouldReceive('sendMessage')
        ->once()
        ->withArgs(fn (string $text, int|string|null $chatId): bool => $text === 'Test message'
            && $chatId === $user->telegram_id);

    new TelegramChannel($bot, new FlagTelegramDeliveryFailure)->send($user, $notification);
});

test('send skips when preconditions are not met', function (object $notifiable, Notification $notification): void {
    $bot = Mockery::mock(Nutgram::class);
    $bot->shouldNotReceive('sendMessage');

    new TelegramChannel($bot, new FlagTelegramDeliveryFailure)->send($notifiable, $notification);
})->with([
    'user has no telegram id' => function (): array {
        $user = User::factory()->create(['telegram_id' => null]);

        $notification = Mockery::mock(Notification::class, SendsTelegramNotification::class);
        $notification->shouldReceive('toTelegram')->with($user)->andReturn(TelegramMessage::create()->text('Test'));

        return [$user, $notification];
    },
    'notification does not implement contract' => function (): array {
        $user = User::factory()->telegramId()->create();

        return [$user, Mockery::mock(Notification::class)];
    },
    'notifiable lacks routeNotificationFor' => function (): array {
        $notifiable = new stdClass;

        $notification = Mockery::mock(Notification::class, SendsTelegramNotification::class);
        $notification->shouldReceive('toTelegram')->with($notifiable)->andReturn(TelegramMessage::create()->text('Test'));

        return [$notifiable, $notification];
    },
]);

test('send swallows known delivery failure and flags user', function (): void {
    $user = User::factory()->telegramId()->create();

    $notification = Mockery::mock(Notification::class, SendsTelegramNotification::class);
    $notification->shouldReceive('toTelegram')
        ->with($user)
        ->andReturn(TelegramMessage::create()->text('Test'));

    $bot = Mockery::mock(Nutgram::class);
    $bot->shouldReceive('sendMessage')
        ->once()
        ->andThrow(new TelegramException('Forbidden: bot was blocked by the user', 403));

    new TelegramChannel($bot, new FlagTelegramDeliveryFailure)->send($user, $notification);

    $user->refresh();
    expect($user->telegram_bot_blocked_at)->not->toBeNull();
});

test('send rethrows unrelated telegram exceptions', function (): void {
    $user = User::factory()->telegramId()->create();

    $notification = Mockery::mock(Notification::class, SendsTelegramNotification::class);
    $notification->shouldReceive('toTelegram')
        ->with($user)
        ->andReturn(TelegramMessage::create()->text('Test'));

    $bot = Mockery::mock(Nutgram::class);
    $bot->shouldReceive('sendMessage')
        ->once()
        ->andThrow(new TelegramException('Too Many Requests: retry after 30', 429));

    new TelegramChannel($bot, new FlagTelegramDeliveryFailure)->send($user, $notification);
})->throws(TelegramException::class, 'Too Many Requests: retry after 30');
