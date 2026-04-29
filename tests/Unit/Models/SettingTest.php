<?php

declare(strict_types=1);

use App\Enums\SettingType;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

beforeEach(fn () => Cache::flush());

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

it('caches trial_period_days result', function (): void {
    Setting::factory()->create(['key' => 'trial_period_days', 'value' => '21']);

    Setting::trialPeriodDays();

    expect(Cache::has(Setting::TRIAL_PERIOD_DAYS_CACHE_KEY))->toBeTrue()
        ->and(Cache::get(Setting::TRIAL_PERIOD_DAYS_CACHE_KEY))->toBe('21');
});

it('flushes trial_period_days cache when setValue is called', function (): void {
    Setting::factory()->create(['key' => 'trial_period_days', 'value' => '14']);
    Setting::trialPeriodDays(); // populate cache

    Setting::setValue('trial_period_days', '30');

    expect(Cache::has(Setting::TRIAL_PERIOD_DAYS_CACHE_KEY))->toBeFalse()
        ->and(Setting::trialPeriodDays())->toBe(30);
});

it('has correct casts', function (): void {
    $setting = Setting::factory()->create(['type' => SettingType::String]);

    expect($setting->id)->toBeInt()
        ->and($setting->type)->toBe(SettingType::String);
});

it('returns null from trackingScripts when not set', function (): void {
    expect(Setting::trackingScripts())->toBeNull();
});

it('returns tracking scripts value when set', function (): void {
    Setting::factory()->create(['key' => 'tracking_scripts', 'value' => '<script>console.log("test")</script>']);

    expect(Setting::trackingScripts())->toBe('<script>console.log("test")</script>');
});

it('caches tracking scripts result', function (): void {
    Setting::factory()->create(['key' => 'tracking_scripts', 'value' => '<script>ga()</script>']);

    Setting::trackingScripts();

    expect(Cache::has(Setting::TRACKING_SCRIPTS_CACHE_KEY))->toBeTrue()
        ->and(Cache::get(Setting::TRACKING_SCRIPTS_CACHE_KEY))->toBe('<script>ga()</script>');
});

it('flushes tracking scripts cache when setValue is called', function (): void {
    Setting::factory()->create(['key' => 'tracking_scripts', 'value' => '<script>ga()</script>']);
    Setting::trackingScripts(); // populate cache

    Setting::setValue('tracking_scripts', '<script>gtag()</script>');

    expect(Cache::has(Setting::TRACKING_SCRIPTS_CACHE_KEY))->toBeFalse()
        ->and(Setting::trackingScripts())->toBe('<script>gtag()</script>');
});
