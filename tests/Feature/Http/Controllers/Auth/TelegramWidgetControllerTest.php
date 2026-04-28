<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Config;

beforeEach(function (): void {
    Config::set('nutgram.token', 'test-bot-token');
});

it('renders the token bridge page for a valid Telegram callback', function (): void {
    $query = signedTelegramWidgetPayload([
        'id' => 777_000_001,
        'first_name' => 'Grace',
        'auth_date' => time(),
    ]);

    $this->get('/auth/telegram/callback?'.http_build_query($query))
        ->assertOk()
        ->assertSee("localStorage.setItem('api_token'", false)
        ->assertSee('app\/track', false);

    expect(User::query()->where('telegram_id', '777000001')->exists())->toBeTrue();
});

it('returns 403 for an invalid Telegram callback', function (): void {
    $this->get('/auth/telegram/callback?id=1&first_name=x&auth_date='.time().'&hash=bad')
        ->assertForbidden();
});
