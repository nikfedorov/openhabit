<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\LocaleService;
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

        if ($locale !== null && in_array($locale, LocaleService::codes(), true)) {
            app()->setLocale($locale);
        }

        /** @var Response */
        return $next($request);
    }
}
