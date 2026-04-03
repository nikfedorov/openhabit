<?php

declare(strict_types=1);

arch()->preset()->php();
arch()->preset()->strict()->ignoring([
    'App\\Http\\Requests',
    'App\\Telegram\\Commands',
]);
arch()->preset()->laravel();
arch()->preset()->security()->ignoring([
    'assert',
]);

arch('controllers')
    ->expect('App\Http\Controllers')
    ->not->toBeUsed();

//
