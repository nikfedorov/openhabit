<?php

declare(strict_types=1);

use App\Telegram\Commands\StartCommand;
use SergiX44\Nutgram\Nutgram;

/** @var Nutgram $bot */
$bot->registerCommand(StartCommand::class);
$bot->fallback([StartCommand::class, 'handle']);
