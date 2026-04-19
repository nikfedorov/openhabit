<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Models\User;
use SergiX44\Nutgram\Telegram\Web\WebAppUser;

final readonly class AuthenticateMiniAppUserAction
{
    /**
     * Find or create the user from Telegram initData and return a new API token.
     */
    public function handle(WebAppUser $webAppUser): string
    {
        $user = User::query()->firstOrCreate(
            ['telegram_id' => (string) $webAppUser->id],
            ['name' => mb_trim(($webAppUser->first_name ?? '').' '.($webAppUser->last_name ?? '')) ?: null],
        );

        $user->last_active_at = now();
        $user->save();

        /** @var string $token */
        $token = $user->createToken('telegram-miniapp')->plainTextToken;

        return $token;
    }
}
