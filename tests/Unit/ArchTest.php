<?php

declare(strict_types=1);

use App\Models\AiModel;
use App\Providers\TelescopeServiceProvider;

arch()->preset()->php();

arch()->preset()->laravel()->ignoring([
    AiModel::class,
    TelescopeServiceProvider::class,
]);

arch()->preset()->security()->ignoring([
    TelescopeServiceProvider::class,
]);

arch('controllers')
    ->expect('App\Http\Controllers')
    ->not->toBeUsed();
