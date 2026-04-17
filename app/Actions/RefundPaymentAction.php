<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Payment;
use App\Models\User;
use SergiX44\Nutgram\Nutgram;

final readonly class RefundPaymentAction
{
    public function __construct(
        private Nutgram $bot,
    ) {}

    public function handle(Payment $payment): void
    {
        /** @var User $user */
        $user = $payment->loadMissing('user')->user;

        if ($payment->is_recurring) {
            $this->bot->editUserStarSubscription(
                telegram_payment_charge_id: $payment->telegram_payment_charge_id,
                is_canceled: true,
                user_id: (int) $user->telegram_id,
            );
        }

        $this->bot->refundStarPayment(
            telegram_payment_charge_id: $payment->telegram_payment_charge_id,
            user_id: (int) $user->telegram_id,
        );
    }
}
