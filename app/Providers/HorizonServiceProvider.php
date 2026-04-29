<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Laravel\Horizon\HorizonApplicationServiceProvider;
use Laravel\Sentinel\Drivers\Driver;
use Laravel\Sentinel\Sentinel;

final class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        parent::boot();

        // Allow Horizon dashboard access in local environment, or via HTTP Basic Auth in production.
        Sentinel::extend('horizon', fn (): Driver => new class(fn () => app()) extends Driver
        {
            public function authorize(Request $request): bool
            {
                if (app()->environment('local')) {
                    return true;
                }

                $username = Config::string('services.horizon.username');
                $password = Config::string('services.horizon.password');

                if (
                    $request->getUser() === $username
                    && hash_equals($password, $request->getPassword() ?? '')
                ) {
                    return true;
                }

                abort(401, 'Unauthorized.', ['WWW-Authenticate' => 'Basic realm="Horizon"']);
            }
        });

        // Horizon::routeSmsNotificationsTo('15556667777');
        // Horizon::routeMailNotificationsTo('example@example.com');
        // Horizon::routeSlackNotificationsTo('slack-webhook-url', '#channel');
    }

    /**
     * Register the Horizon gate.
     *
     * This gate determines who can access Horizon in non-local environments.
     */
    protected function gate(): void
    {
        Gate::define('viewHorizon', fn (?User $user): bool => $user instanceof User);
    }
}
