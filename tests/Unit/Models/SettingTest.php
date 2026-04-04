<?php

declare(strict_types=1);

use App\Enums\SettingType;
use App\Models\Setting;

test('getValue returns value when key exists', function (): void {
    Setting::factory()->create(['key' => 'test_key', 'value' => 'test_value']);

    expect(Setting::getValue('test_key'))->toBe('test_value');
});

test('getValue returns default when key does not exist', function (): void {
    expect(Setting::getValue('missing', 'fallback'))->toBe('fallback');
});

test('getValue returns null when key does not exist and no default', function (): void {
    expect(Setting::getValue('missing'))->toBeNull();
});

test('setValue creates new setting', function (): void {
    Setting::setValue('new_key', 'new_value');

    expect(Setting::getValue('new_key'))->toBe('new_value');
});

test('setValue updates existing setting', function (): void {
    Setting::factory()->create(['key' => 'existing', 'value' => 'old']);

    Setting::setValue('existing', 'new');

    expect(Setting::getValue('existing'))->toBe('new');
});

test('setValue with type parameter sets type', function (): void {
    Setting::setValue('typed_key', 'value', SettingType::Markdown);

    $setting = Setting::query()->where('key', 'typed_key')->first();

    expect($setting->type)->toBe(SettingType::Markdown);
});

test('setValue without type does not override existing type', function (): void {
    Setting::setValue('typed', 'v1', SettingType::Boolean);
    Setting::setValue('typed', 'v2');

    $setting = Setting::query()->where('key', 'typed')->first();

    expect($setting->value)->toBe('v2')
        ->and($setting->type)->toBe(SettingType::Boolean);
});

test('trialPeriodDays returns integer from setting', function (): void {
    Setting::factory()->create(['key' => 'trial_period_days', 'value' => '30']);

    expect(Setting::trialPeriodDays())->toBe(30);
});

test('trialPeriodDays returns 14 as default', function (): void {
    expect(Setting::trialPeriodDays())->toBe(14);
});

test('casts are correct', function (): void {
    $setting = Setting::factory()->create(['type' => SettingType::String]);

    expect($setting->id)->toBeInt()
        ->and($setting->type)->toBe(SettingType::String);
});
