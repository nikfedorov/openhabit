<?php

declare(strict_types=1);

use App\Models\Payment;
use App\Models\User;
use App\Telegram\Handlers\SuccessfulPaymentHandler;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\UpdateType;

it('approves pre-checkout query', function (): void {
    $user = User::factory()->telegramId()->create();

    resolve(Nutgram::class)
        ->comingFrom($user)
        ->hearUpdateType(UpdateType::PRE_CHECKOUT_QUERY, [
            'id' => 'test_query_id',
            'currency' => 'XTR',
            'total_amount' => 1299,
            'invoice_payload' => 'premium',
        ])
        ->reply()
        ->assertCalled('answerPreCheckoutQuery');
});

it('marks user as premium on successful payment and creates payment record', function (): void {
    $user = User::factory()->telegramId()->create(['subscription_expires_at' => null]);

    $expirationDate = now()->addDays(30)->getTimestamp();

    resolve(Nutgram::class)
        ->comingFrom($user)
        ->hearMessage([
            'successful_payment' => [
                'currency' => 'XTR',
                'total_amount' => 1299,
                'invoice_payload' => 'premium',
                'subscription_expiration_date' => $expirationDate,
                'is_recurring' => true,
                'is_first_recurring' => true,
                'telegram_payment_charge_id' => 'charge_123',
                'provider_payment_charge_id' => 'provider_123',
            ],
        ])
        ->reply()
        ->assertCalled('sendMessage');

    expect($user->refresh()->subscription_expires_at->getTimestamp())->toBe($expirationDate)
        ->and(Payment::query()->where('user_id', $user->id)->count())->toBe(1);
});

it('exits early when message has no payment', function (): void {
    $user = User::factory()->telegramId()->create(['subscription_expires_at' => null]);

    $bot = Nutgram::fake();
    $bot->hearMessage(['text' => 'hello']);

    (new SuccessfulPaymentHandler)($bot);

    expect($user->refresh()->subscription_expires_at)->toBeNull();
    expect(Payment::query()->count())->toBe(0);
});

it('ignores unknown user on successful payment', function (): void {
    resolve(Nutgram::class)
        ->hearMessage([
            'from' => ['id' => 999999999, 'is_bot' => false, 'first_name' => 'Unknown'],
            'successful_payment' => [
                'currency' => 'XTR',
                'total_amount' => 1299,
                'invoice_payload' => 'premium',
                'subscription_expiration_date' => now()->addDays(30)->getTimestamp(),
                'telegram_payment_charge_id' => 'charge_789',
                'provider_payment_charge_id' => 'provider_789',
            ],
        ])
        ->reply();

    expect(User::query()->where('telegram_id', '999999999')->exists())->toBeFalse();
    expect(Payment::query()->count())->toBe(0);
});

it('creates payment but does not update subscription for unknown payload', function (): void {
    $user = User::factory()->telegramId()->create(['subscription_expires_at' => null]);

    resolve(Nutgram::class)
        ->comingFrom($user)
        ->hearMessage([
            'successful_payment' => [
                'currency' => 'XTR',
                'total_amount' => 100,
                'invoice_payload' => 'unknown_product',
                'telegram_payment_charge_id' => 'charge_456',
                'provider_payment_charge_id' => 'provider_456',
            ],
        ])
        ->reply();

    expect($user->refresh()->subscription_expires_at)->toBeNull();
    expect(Payment::query()->where('user_id', $user->id)->count())->toBe(1);
});
