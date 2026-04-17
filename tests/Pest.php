<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Sleep;
use Illuminate\Support\Str;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\User\User as TelegramUser;
use Tests\TestCase;

pest()->extend(TestCase::class)
    ->use(LazilyRefreshDatabase::class)
    ->beforeEach(function (): void {
        Str::createRandomStringsNormally();
        Str::createUuidsNormally();
        Http::preventStrayRequests();
        Process::preventStrayProcesses();
        Sleep::fake();

        $this->freezeTime();
        $this->withoutVite();
    })
    ->in('Browser', 'Feature', 'Unit');

/**
 * Macro to set the current Telegram user from an application user.
 */
Nutgram::macro('comingFrom', function (User $user, array $extra = []) {
    $nameParts = explode(' ', $user->name ?? '', 2);
    $firstName = $nameParts[0] ?: 'User';
    $lastName = $nameParts[1] ?? null;

    $telegramUser = TelegramUser::make(
        id: (int) $user->telegram_id,
        is_bot: false,
        first_name: $firstName,
        last_name: $lastName,
    );

    foreach ($extra as $key => $value) {
        $telegramUser->$key = $value;
    }

    return $this->setCommonUser($telegramUser);
});

expect()->extend('toBeOne', fn () => $this->toBe(1));

function something(): void
{
    // ..
}
