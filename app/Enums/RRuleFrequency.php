<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

/**
 * RRule frequency values (RFC 5545).
 */
enum RRuleFrequency: string implements HasColor, HasLabel
{
    case Daily = 'DAILY';
    case Weekly = 'WEEKLY';
    case Monthly = 'MONTHLY';
    case Yearly = 'YEARLY';

    /**
     * Get human-readable label.
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::Daily => __('habit.freq_daily'),
            self::Weekly => __('habit.freq_weekly'),
            self::Monthly => __('habit.freq_monthly'),
            self::Yearly => __('habit.freq_yearly'),
        };
    }

    /**
     * Get Filament badge color for this frequency.
     */
    public function getColor(): string
    {
        return match ($this) {
            self::Daily => 'gray',
            self::Weekly => 'info',
            self::Monthly => 'warning',
            self::Yearly => 'success',
        };
    }
}
