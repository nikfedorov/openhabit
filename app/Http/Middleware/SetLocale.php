<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class SetLocale
{
    /**
     * Set application locale based on the authenticated user's preference.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->user()?->locale;

        /** @var array<int, string> $locales */
        $locales = config('translatable.locales', []);

        if ($locale !== null && in_array($locale, $locales, true)) {
            app()->setLocale($locale);
        }

        /** @var Response */
        return $next($request);
    }
}
