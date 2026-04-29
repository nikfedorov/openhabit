<?php

declare(strict_types=1);

use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Queue;
use Mockery\MockInterface;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Exceptions\TelegramException;

beforeEach(function (): void {
    Queue::fake();
});

it('refunds a recurring payment by charge_id argument', function (): void {
    $bot = mockRefundBot();
    $payment = createPaymentForRefundCommand();

    $bot->shouldReceive('editUserStarSubscription')->once()->andReturnTrue();
    $bot->shouldReceive('refundStarPayment')->once()->andReturnTrue();

    $this->artisan('app:refund-payment', ['charge_id' => $payment->telegram_payment_charge_id])
        ->expectsConfirmation('Cancel subscription?', 'yes')
        ->expectsConfirmation('Issue refund?', 'yes')
        ->expectsPromptsInfo('✓ Subscription cancelled and refund issued.')
        ->assertSuccessful();

    expect($payment->fresh()->refunded_at)->toBeNull();
});

it('refunds a one-time payment by charge_id argument', function (): void {
    $bot = mockRefundBot();
    $payment = createPaymentForRefundCommand(isRecurring: false);

    $bot->shouldNotReceive('editUserStarSubscription');
    $bot->shouldReceive('refundStarPayment')->once()->andReturnTrue();

    $this->artisan('app:refund-payment', ['charge_id' => $payment->telegram_payment_charge_id])
        ->expectsConfirmation('Issue refund?', 'yes')
        ->expectsPromptsInfo('✓ Refund issued (subscription kept).')
        ->assertSuccessful();

    expect($payment->fresh()->refunded_at)->toBeNull();
});

it('aborts when user declines both subscription cancellation and refund', function (): void {
    $bot = mockRefundBot();
    $payment = createPaymentForRefundCommand();

    $bot->shouldNotReceive('editUserStarSubscription');
    $bot->shouldNotReceive('refundStarPayment');

    $this->artisan('app:refund-payment', ['charge_id' => $payment->telegram_payment_charge_id])
        ->expectsConfirmation('Cancel subscription?', 'no')
        ->expectsConfirmation('Issue refund?', 'no')
        ->expectsPromptsInfo('Nothing to do. Aborted.')
        ->assertSuccessful();

    expect($payment->fresh()->refunded_at)->toBeNull();
});

it('can cancel a subscription without issuing a refund', function (): void {
    $bot = mockRefundBot();
    $payment = createPaymentForRefundCommand();

    $bot->shouldReceive('editUserStarSubscription')->once()->andReturnTrue();
    $bot->shouldNotReceive('refundStarPayment');

    $this->artisan('app:refund-payment', ['charge_id' => $payment->telegram_payment_charge_id])
        ->expectsConfirmation('Cancel subscription?', 'yes')
        ->expectsConfirmation('Issue refund?', 'no')
        ->expectsPromptsInfo('✓ Subscription cancelled (no refund).')
        ->assertSuccessful();

    expect($payment->fresh()->refunded_at)->toBeNull();
});

it('fails when charge_id is not found', function (): void {
    $this->artisan('app:refund-payment', ['charge_id' => 'nonexistent-charge-id'])
        ->expectsPromptsError('No payment found with charge ID: nonexistent-charge-id')
        ->assertFailed();
});

it('fails when payment is already refunded', function (): void {
    $payment = createPaymentForRefundCommand();
    $payment->update(['refunded_at' => now()]);

    $this->artisan('app:refund-payment', ['charge_id' => $payment->telegram_payment_charge_id])
        ->expectsPromptsError('This payment has already been refunded on '.$payment->refresh()->refunded_at?->toDateTimeString().'.')
        ->assertFailed();
});

it('fails when there are no active payments to select interactively', function (): void {
    $bot = mockRefundBot();
    Payment::factory()->refunded()->create();

    $bot->shouldNotReceive('editUserStarSubscription');
    $bot->shouldNotReceive('refundStarPayment');

    $this->artisan('app:refund-payment')
        ->expectsPromptsInfo('No active payments to refund.')
        ->assertFailed();
});

it('can select an active payment interactively', function (): void {
    $bot = mockRefundBot();
    $user = User::factory()->telegramId()->create(['name' => 'Nik Fedorov']);
    $payment = Payment::factory()->for($user)->create(['is_recurring' => true]);

    $bot->shouldReceive('editUserStarSubscription')->once()->andReturnTrue();
    $bot->shouldReceive('refundStarPayment')->once()->andReturnTrue();

    $this->artisan('app:refund-payment')
        ->expectsSearch(
            'Search active payments (1 total)',
            search: 'Nik',
            answers: [$payment->telegram_payment_charge_id => refundSearchAnswer($payment, $user)],
            answer: $payment->telegram_payment_charge_id,
        )
        ->expectsConfirmation('Cancel subscription?', 'yes')
        ->expectsConfirmation('Issue refund?', 'yes')
        ->expectsPromptsInfo('✓ Subscription cancelled and refund issued.')
        ->assertSuccessful();
});

it('fails and does not mark refunded_at when Telegram API throws', function (): void {
    $bot = mockRefundBot();
    $payment = createPaymentForRefundCommand(isRecurring: false);

    $bot->shouldReceive('refundStarPayment')
        ->once()
        ->andThrow(new TelegramException('CHARGE_ALREADY_REFUNDED', 400));

    $this->artisan('app:refund-payment', ['charge_id' => $payment->telegram_payment_charge_id])
        ->expectsConfirmation('Issue refund?', 'yes')
        ->expectsPromptsError('Telegram API error: CHARGE_ALREADY_REFUNDED')
        ->assertFailed();

    expect($payment->fresh()->refunded_at)->toBeNull();
});

function mockRefundBot(): MockInterface
{
    $bot = Mockery::mock(Nutgram::class);

    app()->instance(Nutgram::class, $bot);

    return $bot;
}

function createPaymentForRefundCommand(bool $isRecurring = true): Payment
{
    $user = User::factory()->telegramId()->create();

    return Payment::factory()->for($user)->create([
        'is_recurring' => $isRecurring,
    ]);
}

function refundSearchAnswer(Payment $payment, User $user): string
{
    return sprintf(
        '%s  %s  %s  %s',
        mb_str_pad($payment->formattedAmount, 8),
        mb_str_pad($payment->created_at->toDateString(), 12),
        mb_str_pad($payment->is_recurring ? 'recurring' : 'one-time', 10),
        $user->name,
    );
}
