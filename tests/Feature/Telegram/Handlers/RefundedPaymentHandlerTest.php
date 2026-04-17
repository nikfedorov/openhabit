<?php

declare(strict_types=1);

use App\Models\Payment;
use App\Models\User;
use App\Telegram\Handlers\RefundedPaymentHandler;
use SergiX44\Nutgram\Nutgram;

it('marks payment as refunded and keeps the furthest active subscription expiration', function (): void {
    $user = User::factory()->telegramId()->premium()->create();

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

    resolve(Nutgram::class)
        ->comingFrom($user)
        ->hearMessage([
            'refunded_payment' => [
                'currency' => $payment->currency,
                'total_amount' => $payment->total_amount,
                'invoice_payload' => $payment->invoice_payload,
                'telegram_payment_charge_id' => $payment->telegram_payment_charge_id,
            ],
        ])
        ->reply();

    expect($payment->refresh()->isRefunded)->toBeTrue()
        ->and($user->refresh()->subscription_expires_at->toDateTimeString())
        ->toBe($furthestExpiration->toDateTimeString());
});

it('nullifies subscription when refunding the only payment', function (): void {
    $user = User::factory()->telegramId()->premium()->create();
    $payment = Payment::factory()->for($user)->create();

    resolve(Nutgram::class)
        ->comingFrom($user)
        ->hearMessage([
            'refunded_payment' => [
                'currency' => $payment->currency,
                'total_amount' => $payment->total_amount,
                'invoice_payload' => $payment->invoice_payload,
                'telegram_payment_charge_id' => $payment->telegram_payment_charge_id,
            ],
        ])
        ->reply();

    expect($payment->refresh()->isRefunded)->toBeTrue()
        ->and($user->refresh()->subscription_expires_at)->toBeNull();
});

it('ignores unknown charge id', function (): void {
    resolve(Nutgram::class)
        ->hearMessage([
            'refunded_payment' => [
                'currency' => 'XTR',
                'total_amount' => 100,
                'invoice_payload' => 'premium',
                'telegram_payment_charge_id' => 'unknown_charge_id',
            ],
        ])
        ->reply();

    expect(Payment::query()->whereNotNull('refunded_at')->count())->toBe(0);
});

it('exits early when message has no refunded payment', function (): void {
    $bot = Nutgram::fake();
    $bot->hearMessage(['text' => 'hello']);

    resolve(RefundedPaymentHandler::class)($bot);

    expect(Payment::query()->whereNotNull('refunded_at')->count())->toBe(0);
});
