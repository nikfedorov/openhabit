<?php

declare(strict_types=1);

use App\Models\Payment;
use App\Models\User;

it('belongs to user', function (): void {
    $payment = Payment::factory()->create();

    expect($payment->user)->toBeInstanceOf(User::class);
});

it('returns true when refunded_at is set', function (): void {
    $payment = Payment::factory()->create(['refunded_at' => now()]);

    expect($payment->isRefunded)->toBeTrue();
});

it('returns false when refunded_at is null', function (): void {
    $payment = Payment::factory()->create(['refunded_at' => null]);

    expect($payment->isRefunded)->toBeFalse();
});

it('returns formatted amount as stars', function (): void {
    $payment = Payment::factory()->create(['total_amount' => 100]);

    expect($payment->formattedAmount)->toBe('100 ⭐');
});

it('has correct casts', function (): void {
    $payment = Payment::factory()->create();

    expect($payment->id)->toBeInt()
        ->and($payment->user_id)->toBeString()
        ->and($payment->total_amount)->toBeInt()
        ->and($payment->is_recurring)->toBeBool()
        ->and($payment->is_first_recurring)->toBeBool();
});
