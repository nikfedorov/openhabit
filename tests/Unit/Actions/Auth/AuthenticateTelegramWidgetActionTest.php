<?php

declare(strict_types=1);

use App\Actions\Auth\AuthenticateTelegramWidgetAction;
use App\Models\User;
use Illuminate\Support\Facades\Config;

beforeEach(function (): void {
    Config::set('nutgram.token', 'test-bot-token');
});

it('creates a new user from a valid payload and returns the user', function (): void {
    $payload = signedTelegramWidgetPayload([
        'id' => 555_000_001,
        'first_name' => 'Ada',
        'last_name' => 'Lovelace',
        'username' => 'adalovelace',
        'auth_date' => time(),
    ]);

    $user = resolve(AuthenticateTelegramWidgetAction::class)->handle($payload);

    expect($user)->toBeInstanceOf(User::class)
        ->and($user->name)->toBe('Ada Lovelace')
        ->and($user->telegram_username)->toBe('adalovelace')
        ->and($user->last_active_at)->not->toBeNull();
});

it('reuses an existing user and updates username and last_active_at', function (): void {
    User::factory()->create([
        'telegram_id' => '555000002',
        'telegram_username' => 'old',
    ]);

    $payload = signedTelegramWidgetPayload([
        'id' => 555_000_002,
        'first_name' => 'New',
        'username' => 'updated',
        'auth_date' => time(),
    ]);

    resolve(AuthenticateTelegramWidgetAction::class)->handle($payload);

    expect(User::query()->where('telegram_id', '555000002')->count())->toBe(1)
        ->and(User::query()->where('telegram_id', '555000002')->value('telegram_username'))->toBe('updated');
});

it('ignores non-scalar fields when computing the signature', function (): void {
    $signed = signedTelegramWidgetPayload([
        'id' => 555_000_005,
        'first_name' => 'Scalar',
        'auth_date' => time(),
    ]);

    /** @var array<string, mixed> $payload */
    $payload = $signed + ['extra' => ['nested' => 'array']];

    $user = resolve(AuthenticateTelegramWidgetAction::class)->handle($payload);

    expect($user)->toBeInstanceOf(User::class);
});

it('rejects an unconfigured bot token', function (): void {
    Config::set('nutgram.token', '');

    resolve(AuthenticateTelegramWidgetAction::class)->handle([
        'id' => 1, 'auth_date' => time(), 'hash' => 'x',
    ]);
})->throws(InvalidArgumentException::class);

it('rejects invalid payloads', function (array $payload): void {
    resolve(AuthenticateTelegramWidgetAction::class)->handle($payload);
})->throws(InvalidArgumentException::class)->with([
    'missing required fields' => [['first_name' => 'Anon']],
    'non-scalar hash' => [['id' => 1, 'auth_date' => time(), 'hash' => ['array']]],
    'non-numeric auth_date' => [['id' => 1, 'auth_date' => 'not-a-number', 'hash' => 'x']],
    'stale auth_date' => [fn (): array => signedTelegramWidgetPayload(['id' => 1, 'first_name' => 'Old', 'auth_date' => time() - 86_401])],
    'tampered hash' => [fn (): array => array_merge(
        signedTelegramWidgetPayload(['id' => 1, 'first_name' => 'Mallory', 'auth_date' => time()]),
        ['hash' => str_repeat('0', 64)],
    )],
]);
