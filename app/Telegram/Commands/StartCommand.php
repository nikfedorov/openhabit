<?php

declare(strict_types=1);

namespace App\Telegram\Commands;

use App\Models\User;
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
                'last_active_at' => now(),
            ],
        );

        if ($user->wasRecentlyCreated) {
            $bot->sendMessage(
                text: '👋 Welcome to '.Config::string('app.name').", {$user->name}!\n\n"
                    .'Your account has been created automatically.',
                reply_markup: $this->buildWebAppKeyboard(),
            );

            return;
        }

        $user->last_active_at = now();
        if (blank($user->name)) {
            $user->name = $name;
        }

        if (blank($user->locale)) {
            $user->locale = $telegramUser->language_code;
        }

        $user->save();

        $bot->sendMessage(
            text: sprintf('👋 Welcome back, %s!', $user->name),
            reply_markup: $this->buildWebAppKeyboard(),
        );
    }

    private function buildWebAppKeyboard(): InlineKeyboardMarkup
    {
        $webAppUrl = Config::string('app.url').'/telegram-miniapp';

        return InlineKeyboardMarkup::make()
            ->addRow(
                InlineKeyboardButton::make(
                    text: '🚀 Open App',
                    web_app: WebAppInfo::make($webAppUrl),
                ),
            );
    }
}
