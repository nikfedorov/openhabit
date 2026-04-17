<?php

declare(strict_types=1);

namespace App\Actions\Payments;

use App\Models\Payment;
use App\Models\User;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Exceptions\TelegramException;

final readonly class RefundPaymentAction
{
    public function __construct(
        private Nutgram $bot,
    ) {}

    /**
     * Issue a refund via Telegram. The refunded_at column is updated by the refund webhook handler.
     */
    public function handle(Payment $payment, bool $cancelSubscription = true, bool $issueRefund = true): void
    {
        $telegramUserId = $this->telegramUserId($payment);

        if ($cancelSubscription && $payment->is_recurring) {
            $this->cancelSubscription($payment, $telegramUserId);
        }

        if ($issueRefund) {
            $this->issueRefund($payment, $telegramUserId);
        }
    }

    private function cancelSubscription(Payment $payment, int $telegramUserId): void
    {
        try {
            $this->bot->editUserStarSubscription(
                telegram_payment_charge_id: $payment->telegram_payment_charge_id,
                is_canceled: true,
                user_id: $telegramUserId,
            );
        } catch (TelegramException $telegramException) {
            throw_unless($this->subscriptionWasAlreadyCancelled($telegramException), $telegramException);
        }
    }

    private function issueRefund(Payment $payment, int $telegramUserId): void
    {
        $this->bot->refundStarPayment(
            telegram_payment_charge_id: $payment->telegram_payment_charge_id,
            user_id: $telegramUserId,
        );
    }

    private function telegramUserId(Payment $payment): int
    {
        /** @var User $user */
        $user = $payment->loadMissing('user')->user;

        return (int) $user->telegram_id;
    }

    private function subscriptionWasAlreadyCancelled(TelegramException $telegramException): bool
    {
        return str_contains($telegramException->getMessage(), 'SUBSCRIPTION_NOT_MODIFIED');
    }
}
