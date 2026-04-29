<?php

declare(strict_types=1);

use App\Models\Invoice;
use Illuminate\Support\Facades\Cache;

beforeEach(fn () => Cache::flush());

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

it('caches premium link to avoid repeated db queries', function (): void {
    Invoice::factory()->create([
        'slug' => 'premium',
        'invoice_link' => 'https://t.me/test-link',
    ]);

    Invoice::premiumLink();

    $cacheKey = sprintf('invoice:premium:link:%s', app()->getLocale());
    expect(Cache::get($cacheKey))->toBe(['link' => 'https://t.me/test-link']);
});

it('caches null when no premium invoice exists', function (): void {
    Invoice::premiumLink();

    $cacheKey = sprintf('invoice:premium:link:%s', app()->getLocale());
    expect(Cache::get($cacheKey))->toBe(['link' => null]);
});

it('has correct casts', function (): void {
    $invoice = Invoice::factory()->create();

    expect($invoice->id)->toBeInt()
        ->and($invoice->stars)->toBeInt()
        ->and($invoice->subscription_period)->toBeInt();
});
