<?php

declare(strict_types=1);

use App\Models\Invoice;

test('premiumLink returns link for premium invoice', function (): void {
    Invoice::factory()->create([
        'slug' => 'premium',
        'invoice_link' => 'https://t.me/test-link',
    ]);

    expect(Invoice::premiumLink())->toBe('https://t.me/test-link');
});

test('premiumLink returns null when no premium invoice exists', function (): void {
    expect(Invoice::premiumLink())->toBeNull();
});

test('casts are correct', function (): void {
    $invoice = Invoice::factory()->create();

    expect($invoice->id)->toBeInt()
        ->and($invoice->stars)->toBeInt()
        ->and($invoice->subscription_period)->toBeInt();
});
