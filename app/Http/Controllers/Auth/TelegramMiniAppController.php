<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\AuthenticateMiniAppUserAction;
use App\Http\Requests\Auth\TelegramMiniAppRequest;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;

#[Group('Auth', weight: 0)]
final class TelegramMiniAppController
{
    /**
     * Login via Telegram initData.
     */
    public function __invoke(TelegramMiniAppRequest $request, AuthenticateMiniAppUserAction $action): JsonResponse
    {
        $token = $action->handle($request->webAppUser());

        // Mark this session as Telegram-authenticated so the dev token
        // middleware does not overwrite the legitimate token on redirect.
        if ($request->hasSession()) {
            $request->session()->put('telegram_authenticated', true);
        }

        return response()->json([
            /**
             * Indicate successful authentication.
             *
             * @example true
             */
            'success' => true,

            /**
             * The API token for the authenticated user, to be used in subsequent requests.
             *
             * @example 1|AbCdEfGhIjKlMnOpQrStUvWxYz1234567890abcdefg
             */
            'token' => $token,
        ]);
    }
}
