<?php

declare(strict_types=1);

namespace App\Telegram\Handlers;

use App\Models\Payment;
use App\Models\User;
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
            ->with('user')
            ->where('telegram_payment_charge_id', $refundedPayment->telegram_payment_charge_id)
            ->first();

        if ($payment === null) {
            return;
        }

        /** @var User $user */
        $user = $payment->user;

        $payment->update(['refunded_at' => now()]);

        $user->update([
            'subscription_expires_at' => $user->payments()
                ->whereNull('refunded_at')
                ->whereNotNull('subscription_expiration_date')
                ->latest()
                ->value('subscription_expiration_date'),
        ]);
    }
}
