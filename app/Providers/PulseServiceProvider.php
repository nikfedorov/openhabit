<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Laravel\Sentinel\Drivers\Driver;
use Laravel\Sentinel\Sentinel;

final class PulseServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Allow Pulse dashboard access in local environment, or via HTTP Basic Auth in production.
        Sentinel::extend('pulse', fn (): Driver => new class(fn () => app()) extends Driver
        {
            public function authorize(Request $request): bool
            {
                if (app()->environment('local')) {
                    return true;
                }

                $username = Config::string('services.pulse.username');
                $password = Config::string('services.pulse.password');

                if (
                    $request->getUser() === $username
                    && hash_equals($password, $request->getPassword() ?? '')
                ) {
                    return true;
                }

                abort(401, 'Unauthorized.', ['WWW-Authenticate' => 'Basic realm="Pulse"']);
            }
        });
    }
}
