<?php

declare(strict_types=1);

namespace App\Telegram\Handlers;

use App\Actions\Payments\HandleRefundedPaymentAction;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Payment\RefundedPayment;

final readonly class RefundedPaymentHandler
{
    public function __construct(private HandleRefundedPaymentAction $handleRefundedPayment) {}

    public function __invoke(Nutgram $bot): void
    {
        $refundedPayment = $bot->message()?->refunded_payment;

        if (! $refundedPayment instanceof RefundedPayment) {
            return;
        }

        $this->handleRefundedPayment->handle($refundedPayment->telegram_payment_charge_id);
    }
}
