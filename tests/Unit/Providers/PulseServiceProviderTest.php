<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Laravel\Sentinel\Http\Middleware\SentinelMiddleware;

test('viewPulse gate is defined', function (): void {
    expect(Gate::has('viewPulse'))->toBeTrue();
});

test('viewPulse gate allows access in local environment', function (): void {
    /** @phpstan-ignore method.notFound */
    $this->app->detectEnvironment(fn (): string => 'local');

    expect(Gate::allows('viewPulse'))->toBeTrue();
});

test('viewPulse gate denies unauthenticated access in non-local environment', function (): void {
    /** @phpstan-ignore method.notFound */
    $this->app->detectEnvironment(fn (): string => 'production');

    expect(Gate::allows('viewPulse'))->toBeFalse();
});

test('viewPulse gate denies non-admin users in non-local environment', function (): void {
    /** @phpstan-ignore method.notFound */
    $this->app->detectEnvironment(fn (): string => 'production');

    $this->actingAs(User::factory()->create(['is_admin' => false]));

    expect(Gate::allows('viewPulse'))->toBeFalse();
});

test('viewPulse gate allows admin users in non-local environment', function (): void {
    /** @phpstan-ignore method.notFound */
    $this->app->detectEnvironment(fn (): string => 'production');

    $this->actingAs(User::factory()->create(['is_admin' => true]));

    expect(Gate::allows('viewPulse'))->toBeTrue();
});

test('SentinelMiddleware is bound as a pass-through', function (): void {
    $middleware = resolve(SentinelMiddleware::class);
    $request = Request::create('/pulse');
    $sentinel = new stdClass();

    $response = $middleware->handle($request, fn (Request $passed): object => $sentinel, 'pulse');

    expect($response)->toBe($sentinel);
});
