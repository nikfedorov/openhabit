<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Laravel\Sentinel\Http\Middleware\SentinelMiddleware;

test('viewHorizon gate is defined', function (): void {
    expect(Gate::has('viewHorizon'))->toBeTrue();
});

test('viewHorizon gate allows access in local environment', function (): void {
    /** @phpstan-ignore method.notFound */
    $this->app->detectEnvironment(fn (): string => 'local');

    expect(Gate::allows('viewHorizon'))->toBeTrue();
});

test('viewHorizon gate denies unauthenticated access in non-local environment', function (): void {
    /** @phpstan-ignore method.notFound */
    $this->app->detectEnvironment(fn (): string => 'production');

    expect(Gate::allows('viewHorizon'))->toBeFalse();
});

test('viewHorizon gate denies non-admin users in non-local environment', function (): void {
    /** @phpstan-ignore method.notFound */
    $this->app->detectEnvironment(fn (): string => 'production');

    $this->actingAs(User::factory()->create(['is_admin' => false]));

    expect(Gate::allows('viewHorizon'))->toBeFalse();
});

test('viewHorizon gate allows admin users in non-local environment', function (): void {
    /** @phpstan-ignore method.notFound */
    $this->app->detectEnvironment(fn (): string => 'production');

    $this->actingAs(User::factory()->create(['is_admin' => true]));

    expect(Gate::allows('viewHorizon'))->toBeTrue();
});

test('SentinelMiddleware is bound as a pass-through', function (): void {
    $middleware = resolve(SentinelMiddleware::class);
    $request = Request::create('/horizon');
    $sentinel = new stdClass();

    $response = $middleware->handle($request, fn (Request $passed): object => $sentinel, 'horizon');

    expect($response)->toBe($sentinel);
});
