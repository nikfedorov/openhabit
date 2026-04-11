<?php

declare(strict_types=1);

use App\Models\User;

beforeEach(function (): void {
    $this->user = User::factory()->create(['locale' => 'ru']);
});

it('sets app locale from authenticated user locale', function (): void {
    $this->actingAs($this->user, 'sanctum')
        ->getJson('/api/track')
        ->assertOk();

    expect(app()->getLocale())->toBe('ru');
});

it('keeps default locale for missing, invalid, or unauthenticated locale', function (?Closure $setup, string $method): void {
    if ($setup instanceof Closure) {
        $setup($this->user);
    }

    $request = $method === 'authenticated'
        ? $this->actingAs($this->user, 'sanctum')->getJson('/api/track')
        : $this->getJson('/api/track');

    $method === 'authenticated' ? $request->assertOk() : $request->assertUnauthorized();

    expect(app()->getLocale())->toBe('en');
})->with([
    'no locale' => [fn (User $user) => $user->update(['locale' => null]), 'authenticated'],
    'invalid locale' => [fn (User $user) => $user->update(['locale' => 'xx']), 'authenticated'],
    'unauthenticated' => [null, 'guest'],
]);
