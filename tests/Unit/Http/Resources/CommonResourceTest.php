<?php

declare(strict_types=1);

use App\Http\Resources\CommonResource;
use App\Models\User;
use Illuminate\Http\Request;

test('it returns navigation translations', function (): void {
    $user = User::factory()->make();

    $resource = new CommonResource($user);
    $result = $resource->toArray(new Request);

    expect($result)
        ->navigationTranslations->toBeArray()
        ->navigationTranslations->toHaveKeys(['track', 'view']);
});
