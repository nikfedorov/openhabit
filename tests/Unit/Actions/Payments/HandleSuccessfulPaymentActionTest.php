<?php

declare(strict_types=1);

use App\Actions\Payments\HandleSuccessfulPaymentAction;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;
use SergiX44\Nutgram\Telegram\Types\Payment\SuccessfulPayment;

it('stores a premium payment and activates the subscription', function (): void {
    $user = User::factory()->create(['subscription_expires_at' => null]);
    $expirationDate = now()->addDays(30)->getTimestamp();

    $shouldNotify = resolve(HandleSuccessfulPaymentAction::class)->handle(
        SuccessfulPayment::fromArray(successfulPaymentAttributes([
            'subscription_expiration_date' => $expirationDate,
            'is_recurring' => true,
            'is_first_recurring' => true,
        ])),
        $user,
    );

    expect($shouldNotify)->toBeTrue()
        ->and($user->refresh()->subscription_expires_at?->getTimestamp())->toBe($expirationDate)
        ->and(Payment::query()->where('user_id', $user->id)->count())->toBe(1);
});

it('stores a payment without activating premium for non-premium payloads', function (): void {
    $user = User::factory()->create(['subscription_expires_at' => null]);

    $shouldNotify = resolve(HandleSuccessfulPaymentAction::class)->handle(
        SuccessfulPayment::fromArray(successfulPaymentAttributes([
            'invoice_payload' => 'unknown_product',
            'subscription_expiration_date' => null,
        ])),
        $user,
    );

    expect($shouldNotify)->toBeFalse()
        ->and($user->refresh()->subscription_expires_at)->toBeNull()
        ->and(Payment::query()->where('user_id', $user->id)->count())->toBe(1);
});

it('logs payment to the telegram-payments channel', function (): void {
    $channelSpy = Mockery::spy(LoggerInterface::class);
    Log::shouldReceive('channel')->with('telegram-payments')->once()->andReturn($channelSpy);

    $user = User::factory()->create();

    resolve(HandleSuccessfulPaymentAction::class)->handle(
        SuccessfulPayment::fromArray(successfulPaymentAttributes(['total_amount' => 500, 'currency' => 'XTR'])),
        $user,
    );

    $channelSpy->shouldHaveReceived('info')
        ->once()
        ->withArgs(fn (string $message): bool => str_contains($message, '500') && str_contains($message, 'XTR'));
});

it('is idempotent when the same charge id is delivered twice', function (): void {
    $user = User::factory()->create(['subscription_expires_at' => null]);
    $expirationDate = now()->addDays(30)->getTimestamp();
    $attributes = successfulPaymentAttributes([
        'subscription_expiration_date' => $expirationDate,
        'is_recurring' => true,
        'is_first_recurring' => true,
        'telegram_payment_charge_id' => 'duplicate_charge',
    ]);

    $action = resolve(HandleSuccessfulPaymentAction::class);

    $first = $action->handle(SuccessfulPayment::fromArray($attributes), $user);
    $second = $action->handle(SuccessfulPayment::fromArray($attributes), $user);

    expect($first)->toBeTrue()
        ->and($second)->toBeFalse()
        ->and(Payment::query()->where('telegram_payment_charge_id', 'duplicate_charge')->count())->toBe(1);
});

/**
 * @return array<string, bool|int|string|null>
 */
function successfulPaymentAttributes(array $overrides = []): array
{
    return [
        'currency' => 'XTR',
        'total_amount' => 1299,
        'invoice_payload' => 'premium',
        'subscription_expiration_date' => null,
        'is_recurring' => false,
        'is_first_recurring' => false,
        'telegram_payment_charge_id' => 'charge_123',
        'provider_payment_charge_id' => 'provider_123',
        ...$overrides,
    ];
}
