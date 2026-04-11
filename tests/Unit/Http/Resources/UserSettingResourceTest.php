<?php

declare(strict_types=1);

use App\Enums\Theme;
use App\Http\Resources\UserSettingResource;
use App\Models\User;
use Illuminate\Http\Request;

test('it returns locale and theme', function (): void {
    $user = User::factory()->make(['theme' => Theme::Dark]);

    app()->setLocale('ru');

    $resource = new UserSettingResource($user);
    $result = $resource->toArray(new Request);

    expect($result)
        ->settings->toBeArray()
        ->settings->locale->toBe('ru')
        ->settings->theme->toBe('dark');
});

test('it defaults to system when theme is null', function (): void {
    $user = User::factory()->make(['theme' => null]);

    $resource = new UserSettingResource($user);
    $result = $resource->toArray(new Request);

    expect($result)
        ->settings->theme->toBe('system');
});
