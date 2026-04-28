<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Config;
use InvalidArgumentException;

final readonly class AuthenticateTelegramWidgetAction
{
    /**
     * Maximum age of the Telegram Login Widget auth_date payload in seconds.
     */
    private const int MAX_AUTH_AGE_SECONDS = 86_400;

    /**
     * Validate a Telegram Login Widget payload, find or create the user,
     * and return a fresh API token.
     *
     * @param  array<string, mixed>  $payload  Raw query parameters from the
     *                                         widget redirect (id, first_name,
     *                                         last_name, username, photo_url,
     *                                         auth_date, hash).
     *
     * @throws InvalidArgumentException When the payload signature or freshness
     *                                  cannot be validated.
     */
    public function handle(array $payload): string
    {
        $token = Config::string('nutgram.token', '');
        throw_if($token === '', InvalidArgumentException::class, 'Telegram bot token is not configured');

        throw_unless($this->isValid($payload, $token), InvalidArgumentException::class, 'Invalid Telegram authentication payload');

        /** @var array{id: int|string, first_name?: string, last_name?: string, username?: string} $payload */
        $telegramId = (string) $payload['id'];
        $firstName = $payload['first_name'] ?? '';
        $lastName = $payload['last_name'] ?? '';
        $username = $payload['username'] ?? null;

        $user = User::query()->firstOrCreate(
            ['telegram_id' => $telegramId],
            [
                'name' => mb_trim($firstName.' '.$lastName) ?: null,
                'telegram_username' => $username,
                'last_active_at' => now(),
            ],
        );

        if (! $user->wasRecentlyCreated) {
            $user->telegram_username = $username;
            $user->last_active_at = now();
            $user->save();
        }

        /** @var string $plainText */
        $plainText = $user->createToken('telegram-widget')->plainTextToken;

        return $plainText;
    }

    /**
     * Verify the HMAC-SHA256 signature and freshness per Telegram's spec.
     *
     * @see https://core.telegram.org/widgets/login#checking-authorization
     *
     * @param  array<string, mixed>  $payload
     */
    private function isValid(array $payload, string $botToken): bool
    {
        if (! isset($payload['hash'], $payload['auth_date'], $payload['id'])) {
            return false;
        }

        $hash = $payload['hash'];
        $authDate = $payload['auth_date'];
        if (! is_scalar($hash) || ! is_numeric($authDate)) {
            return false;
        }

        $checkHash = (string) $hash;

        // Reject payloads older than the configured window.
        if (abs(time() - (int) $authDate) > self::MAX_AUTH_AGE_SECONDS) {
            return false;
        }

        // Build the data-check-string: sorted "key=value" pairs joined by "\n".
        $fields = $payload;
        unset($fields['hash']);

        $pairs = [];
        foreach ($fields as $key => $value) {
            // Telegram only signs scalar fields.
            if (! is_scalar($value)) {
                continue;
            }

            $pairs[] = $key.'='.$value;
        }

        sort($pairs);

        $dataCheckString = implode("\n", $pairs);

        // Secret key = SHA256(bot_token), then HMAC-SHA256 the check string.
        $secretKey = hash('sha256', $botToken, true);
        $computedHash = hash_hmac('sha256', $dataCheckString, $secretKey);

        return hash_equals($computedHash, $checkHash);
    }
}
