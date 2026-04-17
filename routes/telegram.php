<?php

declare(strict_types=1);

use App\Telegram\Commands\StartCommand;
use App\Telegram\Handlers\PreCheckoutQueryHandler;
use App\Telegram\Handlers\RefundedPaymentHandler;
use App\Telegram\Handlers\SuccessfulPaymentHandler;
use SergiX44\Nutgram\Nutgram;

/** @var Nutgram $bot */
$bot->registerCommand(StartCommand::class);
$bot->fallback([StartCommand::class, 'handle']);

// Payment handlers
$bot->onPreCheckoutQuery([PreCheckoutQueryHandler::class, '__invoke']);
$bot->onSuccessfulPayment([SuccessfulPaymentHandler::class, '__invoke']);
$bot->onRefundedPayment([RefundedPaymentHandler::class, '__invoke']);
