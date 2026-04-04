<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Application theme settings.
 */
enum Theme: string
{
    case Light = 'light';
    case Dark = 'dark';
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
