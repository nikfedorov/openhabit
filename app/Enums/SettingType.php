<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

/**
 * Setting value types.
 */
enum SettingType: string implements HasLabel
{
    case String = 'string';
    case Boolean = 'boolean';
    case Number = 'number';
    case Text = 'text';
    case Markdown = 'markdown';

    public function getLabel(): string
    {
        return ucfirst($this->value);
    }
}
