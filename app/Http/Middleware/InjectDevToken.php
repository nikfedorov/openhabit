<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

final class InjectDevToken
{
    /**
     * Injects a dev token into the SPA shell on local environments.
     *
     * The token is scoped to the current PHP session so that different browser
     * contexts (e.g. a desktop browser and a Telegram Mini App WebView) each
     * carry their own independent token. This prevents one context from
     * invalidating another's token by re-generating the shared "dev" token.
     *
     * Skipped when the session was authenticated via the Telegram Mini App
     * auth flow so that the legitimate Telegram user token is never overwritten.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->isLocal() && ! $request->session()->get('telegram_authenticated', false)) {
            $user = User::query()->oldest()->first();

            if ($user !== null) {
                /** @var non-falsy-string $tokenName */
                $tokenName = 'dev_'.$request->session()->getId();

                /** @var string|null $plaintext */
                $plaintext = $request->session()->get('dev_token');

                // Create a new token only if this session doesn't have one yet,
                // or if the token was pruned from the database.
                if ($plaintext === null || ! $user->tokens()->where('name', $tokenName)->exists()) {
                    $user->tokens()->where('name', $tokenName)->delete();
                    $plaintext = $user->createToken($tokenName)->plainTextToken;
                    $request->session()->put('dev_token', $plaintext);
                }

                View::share('devToken', $plaintext);
            }
        }

        return $next($request);
    }
}
