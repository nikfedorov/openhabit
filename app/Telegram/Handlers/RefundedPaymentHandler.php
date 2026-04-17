<?php

declare(strict_types=1);

namespace App\Telegram\Handlers;

use App\Models\Payment;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Payment\RefundedPayment;

final class RefundedPaymentHandler
{
    public function __invoke(Nutgram $bot): void
    {
        $refundedPayment = $this->refundedPayment($bot);

        if (! $refundedPayment instanceof RefundedPayment) {
            return;
        }

        $payment = $this->findPaymentByChargeId($refundedPayment->telegram_payment_charge_id);

        if (! $payment instanceof Payment) {
            return;
        }

        DB::transaction(function () use ($payment): void {
            $payment->update(['refunded_at' => now()]);

            $payment->user()->update([
                'subscription_expires_at' => $this->latestActiveSubscriptionExpirationForUser($payment->user_id),
            ]);
        });
    }

    private function refundedPayment(Nutgram $bot): ?RefundedPayment
    {
        $payment = $bot->message()?->refunded_payment;

        return $payment instanceof RefundedPayment ? $payment : null;
    }

    private function findPaymentByChargeId(string $chargeId): ?Payment
    {
        return Payment::query()
            ->select(['id', 'user_id'])
            ->where('telegram_payment_charge_id', $chargeId)
            ->first();
    }

    private function latestActiveSubscriptionExpirationForUser(string $userId): ?CarbonInterface
    {
        /** @var Payment|null $latestPayment */
        $latestPayment = Payment::query()
            ->select(['subscription_expiration_date'])
            ->where('user_id', $userId)
            ->whereNull('refunded_at')
            ->whereNotNull('subscription_expiration_date')
            ->latest('subscription_expiration_date')
            ->first();

        return $latestPayment?->subscription_expiration_date;
    }
}
