<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\AuthenticateTelegramWidgetAction;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class TelegramWidgetController
{
    /**
     * Handle the redirect from the Telegram Login Widget.
     *
     * The widget redirects the browser here with id/first_name/.../hash
     * query parameters. We validate the HMAC, log the user into the web
     * session, mint an API token, and render a tiny page that stores the
     * token in localStorage before redirecting to the SPA.
     */
    public function __invoke(Request $request, AuthenticateTelegramWidgetAction $action): View
    {
        try {
            /** @var array<string, mixed> $payload */
            $payload = $request->query();
            $user = $action->handle($payload);
        } catch (InvalidArgumentException $invalidArgumentException) {
            throw new HttpException(403, $invalidArgumentException->getMessage(), $invalidArgumentException);
        }

        Auth::login($user, remember: true);

        /** @var string $token */
        $token = $user->createToken('telegram-widget')->plainTextToken;

        return view('auth.telegram-callback', [
            'token' => $token,
            'redirectUrl' => '/app/track',
        ]);
    }
}
