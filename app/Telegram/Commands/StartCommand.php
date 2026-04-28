<?php

declare(strict_types=1);

namespace App\Telegram\Commands;

use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use SergiX44\Nutgram\Handlers\Type\Command;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\User\User as TelegramUser;
use SergiX44\Nutgram\Telegram\Types\WebApp\WebAppInfo;

final class StartCommand extends Command
{
    protected string $command = 'start';

    protected ?string $description = 'Start the bot';

    public function handle(Nutgram $bot): void
    {
        $telegramId = (string) $bot->userId();
        $telegramUser = $bot->user();
        App::setLocale($telegramUser->language_code ?? Config::string('app.locale'));

        if (! $telegramUser instanceof TelegramUser) {
            return; // @codeCoverageIgnore
        }

        $name = mb_trim(Str::ascii($telegramUser->first_name));
        if (filled($telegramUser->last_name)) {
            $name .= ' '.mb_trim(Str::ascii($telegramUser->last_name));
        }

        $user = User::query()->firstOrCreate(
            ['telegram_id' => $telegramId],
            [
                'name' => $name,
                'locale' => $telegramUser->language_code,
                'telegram_username' => $telegramUser->username,
                'last_active_at' => now(),
            ],
        );

        if ($user->wasRecentlyCreated) {
            $bot->sendMessage(
                text: __('telegram.welcome', ['app' => Config::string('app.name'), 'name' => $user->name])
                    ."\n\n"
                    .__('telegram.account_created'),
                reply_markup: $this->buildWebAppKeyboard(),
            );

            return;
        }

        if (blank($user->name)) {
            $user->name = $name;
        }

        if (blank($user->locale)) {
            $user->locale = $telegramUser->language_code;
        }

        $user->telegram_username = $telegramUser->username;
        $user->last_active_at = now();
        $user->save();

        $bot->sendMessage(
            text: __('telegram.welcome_back', ['name' => $user->name]),
            reply_markup: $this->buildWebAppKeyboard(),
        );
    }

    private function buildWebAppKeyboard(): InlineKeyboardMarkup
    {
        $webAppUrl = Config::string('app.url').'/telegram-miniapp';

        return InlineKeyboardMarkup::make()
            ->addRow(
                InlineKeyboardButton::make(
                    text: __('telegram.open_app'),
                    web_app: WebAppInfo::make($webAppUrl),
                ),
            );
    }
}
