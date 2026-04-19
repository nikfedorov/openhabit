<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Laravel\Sentinel\Sentinel;

test('viewHorizon gate allows authenticated users', function (): void {
    $user = User::factory()->create();

    expect(Gate::has('viewHorizon'))->toBeTrue();

    $this->actingAs($user);
    expect(Gate::check('viewHorizon'))->toBeTrue();
});

test('viewHorizon gate denies unauthenticated access', function (): void {
    expect(Gate::check('viewHorizon'))->toBeFalse();
});

test('horizon sentinel driver is registered and reflects current environment', function (): void {
    $request = Request::create('/horizon');

    expect(Sentinel::driver('horizon')->authorize($request))->toBe(app()->environment('local'));
});
