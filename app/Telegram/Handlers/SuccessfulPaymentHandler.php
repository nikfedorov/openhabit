<?php

declare(strict_types=1);

namespace App\Telegram\Handlers;

use App\Actions\Payments\HandleSuccessfulPaymentAction;
use App\Models\User;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Payment\SuccessfulPayment;

final readonly class SuccessfulPaymentHandler
{
    public function __construct(private HandleSuccessfulPaymentAction $handleSuccessfulPayment) {}

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

        $shouldNotify = $this->handleSuccessfulPayment->handle($payment, $user);

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
}
