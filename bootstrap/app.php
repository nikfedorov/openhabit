<?php

declare(strict_types=1);

use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Sentinel\Http\Middleware\SentinelMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
    )
    ->withBindings([
        // Sentinel is a hard dependency of Horizon/Pulse/Telescope, and its middleware
        // is hardcoded into their route groups BEFORE the `web` group, where sessions
        // and authentication are not yet available. Replace it with a no-op so those
        // routes fall through to the standard `auth` + `can:` middleware configured
        // in horizon.middleware / pulse.middleware.
        SentinelMiddleware::class => fn (): object => new class
        {
            public function handle(mixed $request, Closure $next, ?string $driver = null): mixed
            {
                return $next($request);
            }
        },
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');

        $middleware->redirectGuestsTo('/');

        $middleware->web(append: [
            SetLocale::class,
        ]);

        $middleware->api(append: [
            SetLocale::class,
        ]);

        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
