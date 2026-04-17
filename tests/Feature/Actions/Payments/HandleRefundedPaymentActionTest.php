<?php

declare(strict_types=1);

use App\Actions\Payments\HandleRefundedPaymentAction;
use App\Models\Payment;
use App\Models\User;

it('marks a payment as refunded and keeps the furthest active subscription expiration', function (): void {
    $user = User::factory()->premium()->create();

    $furthestExpiration = now()->addDays(45);
    Payment::factory()->for($user)->create([
        'subscription_expiration_date' => $furthestExpiration,
        'created_at' => now()->subDay(),
    ]);

    Payment::factory()->for($user)->create([
        'subscription_expiration_date' => now()->addDays(15),
        'created_at' => now(),
    ]);

    $payment = Payment::factory()->for($user)->create([
        'subscription_expiration_date' => now()->addDays(30),
        'created_at' => now()->addDay(),
    ]);

    $handled = resolve(HandleRefundedPaymentAction::class)->handle($payment->telegram_payment_charge_id);

    expect($handled)->toBeTrue()
        ->and($payment->fresh()->isRefunded)->toBeTrue()
        ->and($user->fresh()->subscription_expires_at?->toDateTimeString())
        ->toBe($furthestExpiration->toDateTimeString());
});

it('nullifies the subscription when refunding the only active payment', function (): void {
    $user = User::factory()->premium()->create();
    $payment = Payment::factory()->for($user)->create();

    $handled = resolve(HandleRefundedPaymentAction::class)->handle($payment->telegram_payment_charge_id);

    expect($handled)->toBeTrue()
        ->and($payment->fresh()->isRefunded)->toBeTrue()
        ->and($user->fresh()->subscription_expires_at)->toBeNull();
});

it('returns false for an unknown charge id', function (): void {
    expect(resolve(HandleRefundedPaymentAction::class)->handle('unknown_charge_id'))->toBeFalse();
});
