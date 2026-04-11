<?php

declare(strict_types=1);

use App\Enums\Theme;
use App\Http\Resources\UserSettingResource;
use App\Models\User;
use Illuminate\Http\Request;

it('returns locale and theme', function (): void {
    $user = User::factory()->make(['theme' => Theme::Dark, 'move_completed_to_end' => true]);

    app()->setLocale('ru');

    $resource = new UserSettingResource($user);
    $result = $resource->toArray(new Request);

    expect($result)
        ->settings->toBeArray()
        ->settings->locale->toBe('ru')
        ->settings->theme->toBe('dark')
        ->settings->moveCompletedToEnd->toBeTrue();
});

it('defaults to system when theme is null', function (): void {
    $user = User::factory()->make(['theme' => null, 'move_completed_to_end' => false]);

    $resource = new UserSettingResource($user);
    $result = $resource->toArray(new Request);

    expect($result)
        ->settings->theme->toBe('system')
        ->settings->moveCompletedToEnd->toBeFalse();
});
