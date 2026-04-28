<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Laravel\Sentinel\Sentinel;
use Symfony\Component\HttpKernel\Exception\HttpException;

test('pulse sentinel driver allows access in local environment', function (): void {
    /** @phpstan-ignore method.notFound */
    $this->app->detectEnvironment(fn (): string => 'local');

    $request = Request::create('/pulse');

    expect(Sentinel::driver('pulse')->authorize($request))->toBeTrue();
});

test('pulse sentinel driver rejects unauthenticated access in non-local environment', function (): void {
    /** @phpstan-ignore method.notFound */
    $this->app->detectEnvironment(fn (): string => 'production');

    $request = Request::create('/pulse');

    expect(fn (): bool => Sentinel::driver('pulse')->authorize($request))
        ->toThrow(HttpException::class);
});

test('pulse sentinel driver allows access with valid credentials', function (): void {
    /** @phpstan-ignore method.notFound */
    $this->app->detectEnvironment(fn (): string => 'production');

    config(['services.pulse.username' => 'admin', 'services.pulse.password' => 'secret']);

    $request = Request::create('/pulse', 'GET', [], [], [], ['PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW' => 'secret']);

    expect(Sentinel::driver('pulse')->authorize($request))->toBeTrue();
});

test('pulse sentinel driver rejects invalid credentials', function (): void {
    /** @phpstan-ignore method.notFound */
    $this->app->detectEnvironment(fn (): string => 'production');

    config(['services.pulse.username' => 'admin', 'services.pulse.password' => 'secret']);

    $request = Request::create('/pulse', 'GET', [], [], [], ['PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW' => 'wrong']);

    expect(fn (): bool => Sentinel::driver('pulse')->authorize($request))
        ->toThrow(HttpException::class);
});
