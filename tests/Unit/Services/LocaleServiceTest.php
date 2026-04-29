<?php

declare(strict_types=1);

use App\Services\LocaleService;

it('returns known locale label', function (): void {
    expect(LocaleService::label('en'))->toBe('English')
        ->and(LocaleService::label('ru'))->toBe('Русский')
        ->and(LocaleService::label('ar'))->toBe('العربية');
});

it('uppercases unknown locale code as fallback label', function (): void {
    expect(LocaleService::label('xx'))->toBe('XX');
});

it('identifies rtl locales', function (): void {
    expect(LocaleService::isRtl('ar'))->toBeTrue()
        ->and(LocaleService::isRtl('en'))->toBeFalse();
});

it('returns all configured locales with labels', function (): void {
    $all = LocaleService::all();

    expect($all)->toHaveKey('en', 'English')
        ->toHaveKey('ar', 'العربية')
        ->toHaveCount(count(LocaleService::codes()));
});

it('returns configured locale codes', function (): void {
    expect(LocaleService::codes())->toContain('en', 'ru', 'ar');
});
