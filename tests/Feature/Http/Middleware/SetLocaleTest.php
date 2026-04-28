<?php

declare(strict_types=1);

use App\Http\Middleware\SetLocale;
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

it('respects the ?lang= query parameter and persists it as a cookie', function (): void {
    $response = $this->get('/?lang=es');

    $response->assertOk();

    expect(app()->getLocale())->toBe('es');
    $response->assertCookie(SetLocale::COOKIE, 'es');
});

it('ignores an unsupported ?lang= value', function (): void {
    $this->get('/?lang=xx')->assertOk();

    expect(app()->getLocale())->toBe('en');
});

it('falls back to the preferred_locale cookie for guests', function (): void {
    $this->withCookie(SetLocale::COOKIE, 'pt')
        ->get('/')
        ->assertOk();

    expect(app()->getLocale())->toBe('pt');
});

it('prefers ?lang= over the authenticated user locale', function (): void {
    $this->actingAs($this->user, 'sanctum')
        ->getJson('/api/track?lang=zh')
        ->assertOk();

    expect(app()->getLocale())->toBe('zh');
});
