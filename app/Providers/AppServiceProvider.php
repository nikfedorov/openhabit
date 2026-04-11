<?php

declare(strict_types=1);

namespace App\Providers;

use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Scramble::configure()
            ->withDocumentTransformers(function (OpenApi $openApi): void {
                /** @var SecurityScheme $bearerScheme */
                $bearerScheme = SecurityScheme::http('bearer');
                $openApi->secure($bearerScheme);
            });
    }
}
