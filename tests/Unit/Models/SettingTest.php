<?php

declare(strict_types=1);

use App\Enums\SettingType;
use App\Models\Setting;

it('returns value when key exists', function (): void {
    Setting::factory()->create(['key' => 'test_key', 'value' => 'test_value']);

    expect(Setting::getValue('test_key'))->toBe('test_value');
});

it('returns default when key does not exist', function (): void {
    expect(Setting::getValue('missing', 'fallback'))->toBe('fallback');
});

it('returns null when key does not exist and no default', function (): void {
    expect(Setting::getValue('missing'))->toBeNull();
});

it('creates new setting with setValue', function (): void {
    Setting::setValue('new_key', 'new_value');

    expect(Setting::getValue('new_key'))->toBe('new_value');
});

it('updates existing setting with setValue', function (): void {
    Setting::factory()->create(['key' => 'existing', 'value' => 'old']);

    Setting::setValue('existing', 'new');

    expect(Setting::getValue('existing'))->toBe('new');
});

it('sets type with type parameter', function (): void {
    Setting::setValue('typed_key', 'value', SettingType::Markdown);

    $setting = Setting::query()->where('key', 'typed_key')->first();

    expect($setting->type)->toBe(SettingType::Markdown);
});

it('does not override existing type without type', function (): void {
    Setting::setValue('typed', 'v1', SettingType::Boolean);
    Setting::setValue('typed', 'v2');

    $setting = Setting::query()->where('key', 'typed')->first();

    expect($setting->value)->toBe('v2')
        ->and($setting->type)->toBe(SettingType::Boolean);
});

it('returns integer from setting for trial_period_days', function (): void {
    Setting::factory()->create(['key' => 'trial_period_days', 'value' => '30']);

    expect(Setting::trialPeriodDays())->toBe(30);
});

it('returns default value for trial_period_days when not set', function (): void {
    expect(Setting::trialPeriodDays())->toBe(14);
});

it('has correct casts', function (): void {
    $setting = Setting::factory()->create(['type' => SettingType::String]);

    expect($setting->id)->toBeInt()
        ->and($setting->type)->toBe(SettingType::String);
});
