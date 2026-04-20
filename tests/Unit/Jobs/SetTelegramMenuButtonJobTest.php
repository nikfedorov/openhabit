<?php

declare(strict_types=1);

use App\Jobs\SetTelegramMenuButtonJob;
use App\Models\User;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Testing\FakeNutgram;

beforeEach(function (): void {
    Queue::fake();
});

it('calls setChatMenuButton and sets locale for a telegram user', function (): void {
    /** @var FakeNutgram $bot */
    $bot = resolve(Nutgram::class);

    $user = User::factory()->create([
        'telegram_id' => '42',
        'locale' => 'ru',
    ]);

    new SetTelegramMenuButtonJob($user->id)->handle();

    $bot->assertCalled('setChatMenuButton');
    expect(app()->getLocale())->toBe('ru');
});

it('skips when user cannot receive telegram notifications', function (Closure $setup): void {
    /** @var FakeNutgram $bot */
    $bot = resolve(Nutgram::class);

    $userId = $setup();

    new SetTelegramMenuButtonJob($userId)->handle();

    $bot->assertCalled('setChatMenuButton', 0);
})->with([
    'user not found' => fn (): int => (function (): int {
        Log::shouldReceive('warning')
            ->once()
            ->with('SetTelegramMenuButtonJob: user not found', ['user_id' => 99999999]);

        return 99999999;
    })(),
    'no telegram_id' => fn (): int => User::factory()->create(['telegram_id' => null])->id,
    'bot blocked' => fn (): int => User::factory()->create([
        'telegram_id' => '77',
        'telegram_bot_blocked_at' => Date::now(),
    ])->id,
]);

it('exposes correct queue and tries', function (): void {
    $job = new SetTelegramMenuButtonJob(1);

    expect($job->queue)->toBe('telegram')
        ->and($job->tries)->toBe(3);
});
