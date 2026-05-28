<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use App\Models\Setting;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Exceptions\TelegramException;
use SergiX44\Nutgram\Telegram\Types\User\User;

#[Description('Setup the application (configure Telegram webhook)')]
#[Signature('app:setup')]
final class SetupCommand extends Command
{
    public function handle(Nutgram $bot): int
    {
        $this->setupTelegramWebhook($bot);
        $this->saveBotInfo($bot);

        return 0;
    }

    /**
     * Setup the Telegram webhook.
     */
    private function setupTelegramWebhook(Nutgram $bot): void
    {
        $this->info('Setting up Telegram webhook...');

        try {
            $secretToken = null;
            if (config('nutgram.safe_mode', false)) {
                $secretToken = hash('sha256', Config::string('app.key'));
            }

            $url = Config::string('app.url').'/telegram/webhook';
            $bot->setWebhook(
                url: $url,
                secret_token: $secretToken
            );

            $this->info('Telegram webhook successfully set to: '.$url);
        } catch (ConnectException) {
            $this->error('Failed to connect to Telegram API. Check your network connection.');
        } catch (TelegramException $e) {
            $this->error('Telegram API error: '.$e->getMessage());
        }
    }

    /**
     * Setup bot information in settings.
     */
    private function saveBotInfo(Nutgram $bot): void
    {
        $this->info('Fetching bot information...');

        try {
            $me = $bot->getMe();

            throw_if(! $me instanceof User, TelegramException::class, 'Bot info is null');

            Setting::setValue('telegram_bot_id', (string) $me->id);
            Setting::setValue('telegram_bot_username', $me->username);

            $this->info(sprintf('Bot info saved: @%s (ID: %d)', $me->username, $me->id));
        } catch (ConnectException) {
            $this->error('Failed to connect to Telegram API. Check your network connection.');
        } catch (TelegramException) {
            $this->error('Failed to fetch bot information.');
        }
    }
}
