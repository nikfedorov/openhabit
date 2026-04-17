<?php

declare(strict_types=1);

namespace App\Telegram\Handlers;

use App\Models\Payment;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Payment\SuccessfulPayment;

final class SuccessfulPaymentHandler
{
    public function __invoke(Nutgram $bot): void
    {
        $payment = $this->successfulPayment($bot);

        if (! $payment instanceof SuccessfulPayment) {
            return;
        }

        $user = $this->findUser((string) $bot->userId());

        if (! $user instanceof User) {
            return;
        }

        $subscriptionExpiresAt = $this->subscriptionExpiresAt($payment);

        $shouldNotify = DB::transaction(function () use ($payment, $subscriptionExpiresAt, $user): bool {
            Payment::query()->create($this->paymentAttributes($payment, $subscriptionExpiresAt, $user));

            if (! $this->shouldActivatePremium($payment, $subscriptionExpiresAt)) {
                return false;
            }

            $user->update(['subscription_expires_at' => $subscriptionExpiresAt]);

            return true;
        });

        if (! $shouldNotify) {
            return;
        }

        $bot->sendMessage(
            text: __('app.premium_activated', locale: $user->preferredLocale()),
        );
    }

    private function successfulPayment(Nutgram $bot): ?SuccessfulPayment
    {
        $payment = $bot->message()?->successful_payment;

        return $payment instanceof SuccessfulPayment ? $payment : null;
    }

    private function findUser(string $telegramId): ?User
    {
        /** @var User|null $user */
        $user = User::query()
            ->select(['id', 'locale'])
            ->where('telegram_id', $telegramId)
            ->first();

        return $user;
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
