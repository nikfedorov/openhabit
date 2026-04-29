<?php

declare(strict_types=1);

namespace App\Enums;

enum StatPeriod: string
{
    case Daily = 'daily';
    case Weekly = 'weekly';
    case Yearly = 'yearly';
}
