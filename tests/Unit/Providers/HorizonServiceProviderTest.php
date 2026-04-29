<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Laravel\Sentinel\Sentinel;
use Symfony\Component\HttpKernel\Exception\HttpException;

test('viewHorizon gate allows authenticated users', function (): void {
    $user = User::factory()->create();

    expect(Gate::has('viewHorizon'))->toBeTrue();

    $this->actingAs($user);
    expect(Gate::check('viewHorizon'))->toBeTrue();
});

test('viewHorizon gate denies unauthenticated access', function (): void {
    expect(Gate::check('viewHorizon'))->toBeFalse();
});

test('horizon sentinel driver allows access in local environment', function (): void {
    /** @phpstan-ignore method.notFound */
    $this->app->detectEnvironment(fn (): string => 'local');

    $request = Request::create('/horizon');

    expect(Sentinel::driver('horizon')->authorize($request))->toBeTrue();
});

test('horizon sentinel driver rejects unauthenticated access in non-local environment', function (): void {
    /** @phpstan-ignore method.notFound */
    $this->app->detectEnvironment(fn (): string => 'production');

    $request = Request::create('/horizon');

    expect(fn (): bool => Sentinel::driver('horizon')->authorize($request))
        ->toThrow(HttpException::class);
});

test('horizon sentinel driver allows access with valid credentials', function (): void {
    /** @phpstan-ignore method.notFound */
    $this->app->detectEnvironment(fn (): string => 'production');

    config(['services.horizon.username' => 'admin', 'services.horizon.password' => 'secret']);

    $request = Request::create('/horizon', 'GET', [], [], [], ['PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW' => 'secret']);

    expect(Sentinel::driver('horizon')->authorize($request))->toBeTrue();
});

test('horizon sentinel driver rejects invalid credentials', function (): void {
    /** @phpstan-ignore method.notFound */
    $this->app->detectEnvironment(fn (): string => 'production');

    config(['services.horizon.username' => 'admin', 'services.horizon.password' => 'secret']);

    $request = Request::create('/horizon', 'GET', [], [], [], ['PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW' => 'wrong']);

    expect(fn (): bool => Sentinel::driver('horizon')->authorize($request))
        ->toThrow(HttpException::class);
});
