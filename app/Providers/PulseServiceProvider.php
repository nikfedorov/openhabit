<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

final class PulseServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Override the vendor-defined `viewPulse` gate. Vendor registers its own definition
        // via callAfterResolving(Gate::class, ...). Calling Gate::define() here resolves the
        // Gate (firing the vendor callback first) and then overrides it with our version.
        Gate::define(
            'viewPulse',
            fn (?User $user = null): bool => app()->environment('local') || (bool) $user?->is_admin,
        );
    }
}
