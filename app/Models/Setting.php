<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SettingType;
use Database\Factories\SettingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * @property-read int $id
 * @property-read string $key
 * @property-read string|null $value
 * @property-read SettingType $type
 */
final class Setting extends Model
{
    /** @use HasFactory<SettingFactory> */
    use HasFactory;

    public const string TRIAL_PERIOD_DAYS_CACHE_KEY = 'setting:trial_period_days';

    public const string TRACKING_SCRIPTS_CACHE_KEY = 'setting:tracking_scripts';

    public $timestamps = false;

    /**
     * Get tracking/analytics scripts HTML from settings.
     * Cached when non-null to avoid a DB hit on every page render.
     */
    public static function trackingScripts(): ?string
    {
        return Cache::remember(
            key: self::TRACKING_SCRIPTS_CACHE_KEY,
            ttl: 3600,
            callback: fn (): ?string => self::getValue('tracking_scripts'),
        );
    }

    /**
     * Get trial period in days from settings.
     * Cached across requests to avoid repeated DB queries.
     */
    public static function trialPeriodDays(): int
    {
        return (int) Cache::remember(
            key: self::TRIAL_PERIOD_DAYS_CACHE_KEY,
            ttl: 3600,
            callback: fn (): ?string => self::getValue('trial_period_days', '14'),
        );
    }

    /**
     * Get a setting value by key.
     */
    public static function getValue(string $key, ?string $default = null): ?string
    {
        /** @var string|null $value */
        $value = self::query()->where('key', $key)->value('value');

        return $value ?? $default;
    }

    /**
     * Set a setting value by key.
     */
    public static function setValue(string $key, ?string $value, ?SettingType $type = null): void
    {
        $attributes = ['value' => $value];

        if ($type instanceof SettingType) {
            $attributes['type'] = $type;
        }

        self::query()->updateOrCreate(
            ['key' => $key],
            $attributes,
        );

        match ($key) {
            'trial_period_days' => Cache::forget(self::TRIAL_PERIOD_DAYS_CACHE_KEY),
            'tracking_scripts' => Cache::forget(self::TRACKING_SCRIPTS_CACHE_KEY),
            default => null,
        };
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'type' => SettingType::class,
        ];
    }
}
