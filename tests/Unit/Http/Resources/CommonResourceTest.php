<?php

declare(strict_types=1);

use App\Enums\Theme;
use App\Http\Resources\CommonResource;
use App\Models\User;
use Illuminate\Http\Request;

test('it returns locale and navigation translations', function (): void {
    $user = User::factory()->make();

    app()->setLocale('ru');

    $resource = new CommonResource($user);
    $result = $resource->toArray(new Request);

    expect($result)
        ->locale->toBe('ru')
        ->navigationTranslations->toBeArray()
        ->navigationTranslations->toHaveKeys(['track', 'view']);
});

test('it includes user settings with theme', function (): void {
    $user = User::factory()->make(['theme' => Theme::Light]);

    $resource = new CommonResource($user);
    $result = $resource->toArray(new Request);

    expect($result)
        ->settings->toBeArray()
        ->settings->toHaveKey('theme', 'light');
});
