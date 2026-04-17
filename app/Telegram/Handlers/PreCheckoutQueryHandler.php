<?php

declare(strict_types=1);

namespace App\Telegram\Handlers;

use SergiX44\Nutgram\Nutgram;

final class PreCheckoutQueryHandler
{
    public function __invoke(Nutgram $bot): void
    {
        $bot->answerPreCheckoutQuery(ok: true);
    }
}
