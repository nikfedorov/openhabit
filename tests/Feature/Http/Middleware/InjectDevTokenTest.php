<?php

declare(strict_types=1);

use App\Models\User;

it('injects a dev token into the SPA shell on local environment', function (): void {
    User::factory()->create();

    $this->app->detectEnvironment(fn (): string => 'local');

    $this->get(route('app'))
        ->assertOk()
        ->assertSee('api_token');
});

it('skips dev token injection on non-local environments', function (): void {
    User::factory()->create();

    $this->get(route('app'))
        ->assertOk()
        ->assertDontSee('api_token');
});

it('injects a dev token on spa catch-all routes', function (): void {
    User::factory()->create();

    $this->app->detectEnvironment(fn (): string => 'local');

    $this->get('/app/some/nested/path')
        ->assertOk()
        ->assertSee('api_token');
});

it('does not delete tokens from other sessions when a new session loads the app', function (): void {
    /** @var User $user */
    $user = User::factory()->create();

    $this->app->detectEnvironment(fn (): string => 'local');

    // First session loads /app.
    $this->get(route('app'))->assertOk();

    $firstToken = $user->fresh()->tokens()->first();
    expect($firstToken)->not->toBeNull();

    // Simulate a second independent session (different browser / WebView).
    $this->flushSession();

    $this->get(route('app'))->assertOk();

    // Two independent tokens must exist — the first was NOT deleted.
    expect($user->fresh()->tokens()->count())->toBe(2);
    expect($user->fresh()->tokens()->where('id', $firstToken->id)->exists())->toBeTrue();
});

it('skips dev token injection when the session was authenticated via Telegram', function (): void {
    User::factory()->create();

    $this->app->detectEnvironment(fn (): string => 'local');

    $this->withSession(['telegram_authenticated' => true])
        ->get(route('app'))
        ->assertOk()
        ->assertDontSee('api_token');
});
