<?php

declare(strict_types=1);

use App\Models\User;

test('to array', function (): void {
    $user = User::factory()->create()->refresh();

    expect(array_keys($user->toArray()))
        ->toBe([
            'id',
            'name',
            'email',
            'email_verified_at',
            'created_at',
            'updated_at',
            'telegram_id',
            'locale',
            'last_active_at',
        ]);
});

test('preferred locale returns locale when set', function (): void {
    $user = User::factory()->telegram()->create(['locale' => 'ru']);

    expect($user->preferredLocale())->toBe('ru');
});

test('preferred locale returns en when locale is null', function (): void {
    $user = User::factory()->create(['locale' => null]);

    expect($user->preferredLocale())->toBe('en');
});
