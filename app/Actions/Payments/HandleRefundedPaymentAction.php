<?php

declare(strict_types=1);

namespace App\Actions\Payments;

use App\Models\Payment;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

final readonly class HandleRefundedPaymentAction
{
    /**
     * Mark a payment as refunded and sync the user's active subscription expiration.
     */
    public function handle(string $telegramPaymentChargeId): bool
    {
        $payment = $this->findPaymentByChargeId($telegramPaymentChargeId);

        if (! $payment instanceof Payment) {
            return false;
        }

        DB::transaction(function () use ($payment): void {
            $payment->update(['refunded_at' => now()]);

            $payment->user()->update([
                'subscription_expires_at' => $this->latestActiveSubscriptionExpirationForUser($payment->user_id),
            ]);
        });

        Log::channel('telegram-payments')->info(
            'Payment refunded',
            ['user_id' => $payment->user_id, 'charge_id' => $telegramPaymentChargeId],
        );

        return true;
    }

    private function findPaymentByChargeId(string $chargeId): ?Payment
    {
        /** @var Payment|null $payment */
        $payment = Payment::query()
            ->select(['id', 'user_id'])
            ->where('telegram_payment_charge_id', $chargeId)
            ->first();

        return $payment;
    }

    private function latestActiveSubscriptionExpirationForUser(int $userId): ?CarbonInterface
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
