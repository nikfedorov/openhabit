<?php

declare(strict_types=1);

use App\Models\AiModel;

arch()->preset()->php();
arch()->preset()->strict()->ignoring([
    'App\\Http\\Requests',
    'App\\Models',
    'App\\Telegram\\Commands',
]);
arch()->preset()->laravel()->ignoring([
    AiModel::class,
]);
arch()->preset()->security()->ignoring([
    'assert',
]);

arch('controllers')
    ->expect('App\Http\Controllers')
    ->not->toBeUsed();

//
