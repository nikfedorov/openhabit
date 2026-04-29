<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Attributes\Timeout;
use Illuminate\Queue\Attributes\Tries;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Nutgram\Laravel\Facades\Telegram;
use SergiX44\Nutgram\Telegram\Types\Command\MenuButtonWebApp;
use SergiX44\Nutgram\Telegram\Types\WebApp\WebAppInfo;

/**
 * Sets the Telegram chat menu button for a user to a WebApp button
 * with text translated to the user's locale.
 */
#[Timeout(30)]
#[Tries(3)]
final class SetTelegramMenuButtonJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly int $userId,
    ) {
        $this->onQueue('telegram');
    }

    public function handle(): void
    {
        $user = User::query()->find($this->userId);

        if (! $user instanceof User) {
            Log::warning('SetTelegramMenuButtonJob: user not found', ['user_id' => $this->userId]);

            return;
        }

        if (! $user->canReceiveTelegramNotifications()) {
            return;
        }

        App::setLocale($user->preferredLocale());

        Telegram::setChatMenuButton(
            chat_id: (int) $user->telegram_id,
            menu_button: new MenuButtonWebApp(
                text: __('telegram.open_app'),
                web_app: WebAppInfo::make(Config::string('app.url').'/telegram-miniapp'),
            ),
        );
    }
}
