<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SettingType;
use Database\Factories\SettingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public $timestamps = false;

    /**
     * Get trial period in days from settings.
     * Cached per-request to avoid repeated DB queries from User::hasPremium().
     */
    public static function trialPeriodDays(): int
    {
        return (int) once(fn (): ?string => self::getValue('trial_period_days', '14'));
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
