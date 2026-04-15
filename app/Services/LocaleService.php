<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Shared locale utilities for supported languages.
 */
final class LocaleService
{
    /** @var array<string, string> Locale code => display name */
    private const array LOCALE_LABELS = [
        'en' => 'English',
        'ru' => 'Русский',
        'es' => 'Español',
        'zh' => '中文',
        'hi' => 'हिन्दी',
        'bn' => 'বাংলা',
        'pt' => 'Português',
        'ar' => 'العربية',
    ];

    /** @var array<string> RTL locale codes */
    private const array RTL_LOCALES = ['ar'];

    /**
     * Check if a locale uses right-to-left text direction.
     */
    public static function isRtl(string $locale): bool
    {
        return in_array($locale, self::RTL_LOCALES, true);
    }

    /**
     * Get human-readable label for a locale code.
     */
    public static function label(string $locale): string
    {
        return self::LOCALE_LABELS[$locale] ?? mb_strtoupper($locale);
    }

    /**
     * Get all supported locales with their labels.
     *
     * @return array<string, string>
     */
    public static function all(): array
    {
        /** @var array<string> $configLocales */
        $configLocales = config('translatable.locales', []);

        $result = [];
        foreach ($configLocales as $locale) {
            $result[$locale] = self::label($locale);
        }

        return $result;
    }

    /**
     * Get the list of configured locale codes.
     *
     * @return array<string>
     */
    public static function codes(): array
    {
        /** @var array<string> */
        return config('translatable.locales', []);
    }
}
