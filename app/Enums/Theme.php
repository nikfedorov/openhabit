<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Application theme settings.
 */
enum Theme: string
{
    /** Use a bright background with dark text. */
    case Light = 'light';
    /** Use a dark background with light text. */
    case Dark = 'dark';
    /** Follow the operating system theme preference. */
    case System = 'system';

    /**
     * Get human-readable label.
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::Light => 'Light',
            self::Dark => 'Dark',
            self::System => 'System',
        };
    }

    /**
     * Get icon for the theme option.
     */
    public function getIcon(): string
    {
        return match ($this) {
            self::Light => 'sun',
            self::Dark => 'moon',
            self::System => 'computer',
        };
    }
}
