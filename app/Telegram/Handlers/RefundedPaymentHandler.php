<?php

declare(strict_types=1);

namespace App\Telegram\Handlers;

use App\Models\Payment;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Payment\RefundedPayment;

final class RefundedPaymentHandler
{
    public function __invoke(Nutgram $bot): void
    {
        $refundedPayment = $bot->message()?->refunded_payment;

        if (! $refundedPayment instanceof RefundedPayment) {
            return;
        }

        $payment = Payment::query()
            ->select(['id', 'user_id'])
            ->where('telegram_payment_charge_id', $refundedPayment->telegram_payment_charge_id)
            ->first();

        if ($payment === null) {
            return;
        }

        $payment->update(['refunded_at' => now()]);

        $payment->user()->update([
            'subscription_expires_at' => $this->latestActiveSubscriptionExpiration($payment),
        ]);
    }

    private function latestActiveSubscriptionExpiration(Payment $payment): mixed
    {
        /** @var Payment|null $latestPayment */
        $latestPayment = Payment::query()
            ->select(['id', 'subscription_expiration_date'])
            ->where('user_id', $payment->user_id)
            ->whereNull('refunded_at')
            ->whereNotNull('subscription_expiration_date')
            ->latest('subscription_expiration_date')
            ->first();

        return $latestPayment?->subscription_expiration_date;
    }
}
