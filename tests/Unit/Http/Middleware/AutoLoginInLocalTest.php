<?php

declare(strict_types=1);

use App\Http\Middleware\AutoLoginInLocal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

test('logs in first user when in local environment', function (): void {
    $user = User::factory()->create();

    config(['app.env' => 'local']);

    $middleware = new AutoLoginInLocal;
    $middleware->handle(Request::create('/'), fn () => new Response);

    expect(Auth::check())->toBeTrue()
        ->and(Auth::id())->toBe($user->id);
});

test('does not log in when not in local environment', function (): void {
    User::factory()->create();

    config(['app.env' => 'production']);

    $middleware = new AutoLoginInLocal;
    $middleware->handle(Request::create('/'), fn () => new Response);

    expect(Auth::check())->toBeFalse();
});

test('does not log in when already authenticated', function (): void {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    config(['app.env' => 'local']);
    Auth::login($user2);

    $middleware = new AutoLoginInLocal;
    $middleware->handle(Request::create('/'), fn () => new Response);

    expect(Auth::id())->toBe($user2->id);
});

test('does nothing when no users exist', function (): void {
    config(['app.env' => 'local']);

    $middleware = new AutoLoginInLocal;
    $middleware->handle(Request::create('/'), fn () => new Response);

    expect(Auth::check())->toBeFalse();
});
