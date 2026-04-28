<?php

declare(strict_types=1);

namespace App\Actions\Payments;

use App\Models\Payment;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use SergiX44\Nutgram\Telegram\Types\Payment\SuccessfulPayment;

final readonly class HandleSuccessfulPaymentAction
{
    /**
     * Store the payment and activate premium when this charge represents a subscription.
     */
    public function handle(SuccessfulPayment $payment, User $user): bool
    {
        $subscriptionExpiresAt = $this->subscriptionExpiresAt($payment);

        $activated = DB::transaction(function () use ($payment, $subscriptionExpiresAt, $user): bool {
            Payment::query()->create($this->paymentAttributes($payment, $subscriptionExpiresAt, $user));

            if (! $this->shouldActivatePremium($payment, $subscriptionExpiresAt)) {
                return false;
            }

            $user->update(['subscription_expires_at' => $subscriptionExpiresAt]);

            return true;
        });

        Log::channel('telegram-payments')->info(
            sprintf('Payment received: %d %s from user %d (@%s)', $payment->total_amount, $payment->currency, $user->id, $user->telegram_username ?? 'unknown'),
            ['premium_until' => $subscriptionExpiresAt?->toIso8601String(), 'charge_id' => $payment->telegram_payment_charge_id],
        );

        return $activated;
    }

    private function subscriptionExpiresAt(SuccessfulPayment $payment): ?CarbonImmutable
    {
        return $payment->subscription_expiration_date !== null
            ? CarbonImmutable::createFromTimestamp($payment->subscription_expiration_date)
            : null;
    }

    /**
     * @return array<string, CarbonImmutable|bool|int|string|null>
     */
    private function paymentAttributes(
        SuccessfulPayment $payment,
        ?CarbonImmutable $subscriptionExpiresAt,
        User $user,
    ): array {
        return [
            'user_id' => $user->id,
            'currency' => $payment->currency,
            'total_amount' => $payment->total_amount,
            'invoice_payload' => $payment->invoice_payload,
            'subscription_expiration_date' => $subscriptionExpiresAt,
            'is_recurring' => $payment->is_recurring,
            'is_first_recurring' => $payment->is_first_recurring,
            'telegram_payment_charge_id' => $payment->telegram_payment_charge_id,
            'provider_payment_charge_id' => $payment->provider_payment_charge_id,
        ];
    }

    private function shouldActivatePremium(
        SuccessfulPayment $payment,
        ?CarbonImmutable $subscriptionExpiresAt,
    ): bool {
        return $payment->invoice_payload === 'premium'
            && $subscriptionExpiresAt instanceof CarbonImmutable;
    }
}
