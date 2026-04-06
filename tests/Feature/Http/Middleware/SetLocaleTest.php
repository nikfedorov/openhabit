<?php

declare(strict_types=1);

use App\Models\User;

beforeEach(function (): void {
    $this->user = User::factory()->create(['locale' => 'ru']);
});

test('sets app locale from authenticated user locale', function (): void {
    $this->actingAs($this->user)
        ->get(route('dashboard'))
        ->assertOk();

    expect(app()->getLocale())->toBe('ru');
});

test('keeps default locale for missing, invalid, or unauthenticated locale', function (?Closure $setup, string $method): void {
    if ($setup instanceof Closure) {
        $setup($this->user);
    }

    $request = $method === 'authenticated'
        ? $this->actingAs($this->user)->get(route('dashboard'))
        : $this->get(route('dashboard'));

    $method === 'authenticated' ? $request->assertOk() : $request->assertRedirect();

    expect(app()->getLocale())->toBe('en');
})->with([
    'no locale' => [fn (User $user) => $user->update(['locale' => null]), 'authenticated'],
    'invalid locale' => [fn (User $user) => $user->update(['locale' => 'xx']), 'authenticated'],
    'unauthenticated' => [null, 'guest'],
]);
