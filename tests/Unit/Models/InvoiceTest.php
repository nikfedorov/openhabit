<?php

declare(strict_types=1);

use App\Models\Invoice;

it('returns link for premium invoice', function (): void {
    Invoice::factory()->create([
        'slug' => 'premium',
        'invoice_link' => 'https://t.me/test-link',
    ]);

    expect(Invoice::premiumLink())->toBe('https://t.me/test-link');
});

it('returns null when no premium invoice exists', function (): void {
    expect(Invoice::premiumLink())->toBeNull();
});

it('has correct casts', function (): void {
    $invoice = Invoice::factory()->create();

    expect($invoice->id)->toBeInt()
        ->and($invoice->stars)->toBeInt()
        ->and($invoice->subscription_period)->toBeInt();
});
