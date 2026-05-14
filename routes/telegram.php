<?php

declare(strict_types=1);

use App\Telegram\Callbacks\CompleteHabitCallback;
use App\Telegram\Commands\StartCommand;
use App\Telegram\Handlers\DeliveryFailureHandler;
use App\Telegram\Handlers\PreCheckoutQueryHandler;
use App\Telegram\Handlers\RefundedPaymentHandler;
use App\Telegram\Handlers\SuccessfulPaymentHandler;
use SergiX44\Nutgram\Nutgram;

/** @var Nutgram $bot */
$bot->onException(DeliveryFailureHandler::class);
$bot->registerCommand(StartCommand::class);
$bot->fallback([StartCommand::class, 'handle']);

$bot->onCallbackQueryData('complete_habit:{habitId}', [CompleteHabitCallback::class, '__invoke']);

// No-op handler for the "Done!" button after habit completion
$bot->onCallbackQueryData('noop', fn (Nutgram $bot): ?bool => $bot->answerCallbackQuery());

// Payment handlers
$bot->onPreCheckoutQuery([PreCheckoutQueryHandler::class, '__invoke']);
$bot->onSuccessfulPayment([SuccessfulPaymentHandler::class, '__invoke']);
$bot->onRefundedPayment([RefundedPaymentHandler::class, '__invoke']);
