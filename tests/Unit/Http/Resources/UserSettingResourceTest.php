<?php

declare(strict_types=1);

use App\Enums\Theme;
use App\Http\Resources\UserSettingResource;
use App\Models\User;
use Illuminate\Http\Request;

test('it returns theme setting', function (): void {
    $user = User::factory()->make(['theme' => Theme::Dark]);

    $resource = new UserSettingResource($user);
    $result = $resource->toArray(new Request);

    expect($result)
        ->theme->toBe('dark');
});

test('it defaults to system when theme is null', function (): void {
    $user = User::factory()->make(['theme' => null]);

    $resource = new UserSettingResource($user);
    $result = $resource->toArray(new Request);

    expect($result)
        ->theme->toBe('system');
});
