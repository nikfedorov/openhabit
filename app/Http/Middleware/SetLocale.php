<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\LocaleService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

final class SetLocale
{
    /**
     * Cookie name used to remember a guest's manually selected locale.
     */
    public const string COOKIE = 'preferred_locale';

    /**
     * Set application locale.
     *
     * Priority: explicit ?lang= query > authenticated user's locale > cookie.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $codes = LocaleService::codes();
        $persistGuestLocale = null;

        $queryLocale = $request->query('lang');
        $userLocale = $request->user()?->locale;
        $cookieLocale = $request->cookie(self::COOKIE);

        if (is_string($queryLocale) && in_array($queryLocale, $codes, true)) {
            app()->setLocale($queryLocale);
            $persistGuestLocale = $queryLocale;
        } elseif (is_string($userLocale) && in_array($userLocale, $codes, true)) {
            app()->setLocale($userLocale);
        } elseif (is_string($cookieLocale) && in_array($cookieLocale, $codes, true)) {
            app()->setLocale($cookieLocale);
        }

        /** @var Response $response */
        $response = $next($request);

        if ($persistGuestLocale !== null) {
            // Remember the choice for one year so guests don't have to reselect.
            Cookie::queue(Cookie::make(self::COOKIE, $persistGuestLocale, 60 * 24 * 365));
        }

        return $response;
    }
}
