<?php

declare(strict_types=1);

namespace App\Http\Controllers\Telegram;

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\RunningMode\Webhook;

final class WebhookController
{
    public function __invoke(Nutgram $bot): void
    {
        $bot->setRunningMode(Webhook::class);
        $bot->run();
    }
}
