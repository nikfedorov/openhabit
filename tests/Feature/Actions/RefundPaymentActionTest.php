<?php

declare(strict_types=1);

use App\Actions\RefundPaymentAction;
use App\Models\Payment;
use App\Models\User;
use Mockery\MockInterface;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Exceptions\TelegramException;

it('cancels subscription and refunds recurring payments', function (): void {
    [$payment, $user] = makePaymentForRefundAction();

    $bot = Mockery::mock(Nutgram::class);
    expectSubscriptionCancellation($bot, $payment, $user);
    expectRefund($bot, $payment, $user);

    new RefundPaymentAction($bot)->handle($payment);
});

it('skips subscription cancellation for non-recurring payments', function (): void {
    [$payment, $user] = makePaymentForRefundAction(isRecurring: false);

    $bot = Mockery::mock(Nutgram::class);
    $bot->shouldNotReceive('editUserStarSubscription');
    expectRefund($bot, $payment, $user);

    new RefundPaymentAction($bot)->handle($payment);
});

it('proceeds with refund when subscription is already cancelled (SUBSCRIPTION_NOT_MODIFIED)', function (): void {
    [$payment, $user] = makePaymentForRefundAction();

    $bot = Mockery::mock(Nutgram::class);
    $bot->shouldReceive('editUserStarSubscription')
        ->once()
        ->andThrow(new TelegramException('Bad Request: SUBSCRIPTION_NOT_MODIFIED', 400));
    expectRefund($bot, $payment, $user);

    new RefundPaymentAction($bot)->handle($payment);
});

it('rethrows TelegramException that is not SUBSCRIPTION_NOT_MODIFIED', function (): void {
    [$payment] = makePaymentForRefundAction();

    $bot = Mockery::mock(Nutgram::class);
    $bot->shouldReceive('editUserStarSubscription')
        ->once()
        ->andThrow(new TelegramException('Bad Request: SOME_OTHER_ERROR', 400));
    $bot->shouldNotReceive('refundStarPayment');

    expect(fn () => new RefundPaymentAction($bot)->handle($payment))
        ->toThrow(TelegramException::class, 'SOME_OTHER_ERROR');
});

/**
 * @return array{Payment, User}
 */
function makePaymentForRefundAction(bool $isRecurring = true): array
{
    $user = User::factory()->telegramId()->make();
    $payment = Payment::factory()->make([
        'is_recurring' => $isRecurring,
    ]);

    $payment->setRelation('user', $user);

    return [$payment, $user];
}

function expectSubscriptionCancellation(MockInterface $bot, Payment $payment, User $user): void
{
    $bot->shouldReceive('editUserStarSubscription')
        ->once()
        ->withArgs(fn (string $chargeId, bool $isCanceled, int $userId): bool => $chargeId === $payment->telegram_payment_charge_id
            && $isCanceled
            && $userId === (int) $user->telegram_id)
        ->andReturnTrue();
}

function expectRefund(MockInterface $bot, Payment $payment, User $user): void
{
    $bot->shouldReceive('refundStarPayment')
        ->once()
        ->withArgs(fn (string $chargeId, int $userId): bool => $chargeId === $payment->telegram_payment_charge_id
            && $userId === (int) $user->telegram_id)
        ->andReturnTrue();
}
