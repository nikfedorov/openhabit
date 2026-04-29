<?php

declare(strict_types=1);

use App\Actions\Auth\AuthenticateMiniAppUserAction;
use App\Models\User;
use SergiX44\Nutgram\Telegram\Web\WebAppUser;

function makeWebAppUserForAction(int $id, ?string $first = null, ?string $last = null): WebAppUser
{
    $user = new WebAppUser;
    $user->id = $id;
    if ($first !== null) {
        $user->first_name = $first;
    }

    if ($last !== null) {
        $user->last_name = $last;
    }

    return $user;
}

it('creates a new user with name from first/last name and returns a token', function (): void {
    $token = resolve(AuthenticateMiniAppUserAction::class)->handle(
        makeWebAppUserForAction(999_001, 'Ada', 'Lovelace'),
    );

    expect($token)->toBeString()->not->toBeEmpty();

    $user = User::query()->where('telegram_id', '999001')->firstOrFail();

    expect($user->name)->toBe('Ada Lovelace')
        ->and($user->last_active_at)->not->toBeNull();
});

it('reuses an existing user, updates last_active_at and returns a fresh token', function (): void {
    $existing = User::factory()->create([
        'telegram_id' => '999002',
        'last_active_at' => now()->subDay(),
    ]);

    $token = resolve(AuthenticateMiniAppUserAction::class)->handle(
        makeWebAppUserForAction(999_002, 'Anything'),
    );

    expect($token)->toBeString()->not->toBeEmpty()
        ->and(User::query()->where('telegram_id', '999002')->count())->toBe(1)
        ->and($existing->fresh()?->last_active_at?->toDateTimeString())
        ->toBe(now()->toDateTimeString());
});
