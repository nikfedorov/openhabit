<?php

declare(strict_types=1);

namespace App\Telegram\Handlers;

use App\Models\Payment;
use App\Models\User;
use Carbon\CarbonImmutable;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Payment\SuccessfulPayment;

final class SuccessfulPaymentHandler
{
    public function __invoke(Nutgram $bot): void
    {
        $payment = $bot->message()?->successful_payment;

        if (! $payment instanceof SuccessfulPayment) {
            return;
        }

        $telegramId = (string) $bot->userId();

        $user = User::query()->where('telegram_id', $telegramId)->first();

        if ($user === null) {
            return;
        }

        $subscriptionExpiresAt = $payment->subscription_expiration_date !== null
            ? CarbonImmutable::createFromTimestamp($payment->subscription_expiration_date)
            : null;

        Payment::query()->create([
            'user_id' => $user->id,
            'currency' => $payment->currency,
            'total_amount' => $payment->total_amount,
            'invoice_payload' => $payment->invoice_payload,
            'subscription_expiration_date' => $subscriptionExpiresAt,
            'is_recurring' => $payment->is_recurring,
            'is_first_recurring' => $payment->is_first_recurring,
            'telegram_payment_charge_id' => $payment->telegram_payment_charge_id,
            'provider_payment_charge_id' => $payment->provider_payment_charge_id,
        ]);

        if ($payment->invoice_payload === 'premium' && $subscriptionExpiresAt instanceof CarbonImmutable) {
            $user->update([
                'subscription_expires_at' => $subscriptionExpiresAt,
            ]);

            $bot->sendMessage(
                text: __('app.premium_activated', locale: $user->preferredLocale()),
            );
        }
    }
}
