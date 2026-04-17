<?php

declare(strict_types=1);

use App\Actions\RefundPaymentAction;
use App\Models\Payment;
use App\Models\User;
use SergiX44\Nutgram\Nutgram;

it('cancels subscription and refunds recurring payments', function (): void {
    $user = User::factory()->telegramId()->create();
    $payment = Payment::factory()->for($user)->create(['is_recurring' => true]);

    $bot = Mockery::mock(Nutgram::class);
    $bot->shouldReceive('editUserStarSubscription')
        ->once()
        ->withArgs(fn (string $chargeId, bool $isCanceled, int $userId): bool => $chargeId === $payment->telegram_payment_charge_id
            && $isCanceled
            && $userId === (int) $user->telegram_id)
        ->andReturnTrue();
    $bot->shouldReceive('refundStarPayment')
        ->once()
        ->withArgs(fn (string $chargeId, int $userId): bool => $chargeId === $payment->telegram_payment_charge_id
            && $userId === (int) $user->telegram_id)
        ->andReturnTrue();

    new RefundPaymentAction($bot)->handle($payment);
});

it('skips subscription cancellation for non-recurring payments', function (): void {
    $user = User::factory()->telegramId()->create();
    $payment = Payment::factory()->for($user)->create(['is_recurring' => false]);

    $bot = Mockery::mock(Nutgram::class);
    $bot->shouldNotReceive('editUserStarSubscription');
    $bot->shouldReceive('refundStarPayment')->once()->andReturnTrue();

    new RefundPaymentAction($bot)->handle($payment);
});
