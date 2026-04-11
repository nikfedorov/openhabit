<?php

declare(strict_types=1);

use App\Models\User;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\User\User as TelegramUser;
use SergiX44\Nutgram\Testing\FakeNutgram;

it('creates new user and sends welcome', function (): void {
    /** @var FakeNutgram $bot */
    $bot = resolve(Nutgram::class);

    $bot->hearText('/start')
        ->reply()
        ->assertReply('sendMessage');

    expect(User::query()->count())->toBe(1);
});

it('welcomes back existing user', function (): void {
    /** @var FakeNutgram $bot */
    $bot = resolve(Nutgram::class);

    $telegramUser = TelegramUser::make(
        id: 12345,
        is_bot: false,
        first_name: 'Existing',
        last_name: 'User',
    );

    User::factory()->telegram()->create([
        'telegram_id' => '12345',
        'name' => 'Existing User',
    ]);

    expect(User::query()->count())->toBe(1);
});

it('updates blank name and locale for existing user', function (): void {
    /** @var FakeNutgram $bot */
    $bot = resolve(Nutgram::class);

    $telegramUser = TelegramUser::make(
        id: 99999,
        is_bot: false,
        first_name: 'Updated',
        last_name: 'Name',
    );
    $telegramUser->language_code = 'ru';

    User::factory()->telegram()->create([
        'telegram_id' => '99999',
        'name' => '',
        'locale' => '',
    ]);

    $bot->setCommonUser($telegramUser)
        ->hearText('/start')
        ->reply()
        ->assertReply('sendMessage');

    $user = User::query()->where('telegram_id', '99999')->first();
    expect($user->name)->toBe('Updated Name')
        ->and($user->locale)->toBe('ru');
});
