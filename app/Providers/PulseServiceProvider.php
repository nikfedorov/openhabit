<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Laravel\Pulse\Facades\Pulse;

final class PulseServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Configure the user resolver for Pulse to include the user's name, username, and avatar URL.
        Pulse::user(fn (User $user): array => [
            'name' => $user->name,
            'extra' => $user->telegram_username ? '@'.$user->telegram_username : null,
            'avatar' => $user->telegram_photo_url,
        ]);

        // Override the vendor-defined `viewPulse` gate. Vendor registers its own definition
        // via callAfterResolving(Gate::class, ...). Calling Gate::define() here resolves the
        // Gate (firing the vendor callback first) and then overrides it with our version.
        Gate::define(
            'viewPulse',
            fn (?User $user = null): bool => app()->environment('local') || (bool) $user?->is_admin,
        );
    }
}
