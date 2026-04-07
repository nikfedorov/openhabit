<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Requests\Auth\TelegramMiniAppRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;

final class TelegramMiniAppController
{
    public function __invoke(TelegramMiniAppRequest $request): JsonResponse
    {
        $webAppUser = $request->webAppUser();

        $user = User::query()->firstOrCreate(
            ['telegram_id' => (string) $webAppUser->id],
            ['name' => mb_trim(($webAppUser->first_name ?? '').' '.($webAppUser->last_name ?? '')) ?: null],
        );

        $user->last_active_at = now();
        $user->save();

        /** @var string $token */
        $token = $user->createToken('telegram-miniapp')->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
        ]);
    }
}
