<?php

declare(strict_types=1);

namespace App\Enums;

enum MemoryCategory: string
{
    case LongTerm = 'long_term';
    case ShortTerm = 'short_term';
    case Challenges = 'challenges';
    case Successes = 'successes';
    case Goals = 'goals';
    case Personality = 'personality';

    /**
     * Human-readable label used in AI prompts.
     */
    public function label(): string
    {
        return match ($this) {
            self::LongTerm => 'Long-term observations',
            self::ShortTerm => 'Recent/short-term context',
            self::Challenges => 'Main challenges & struggles',
            self::Successes => 'Achievements & successes',
            self::Goals => 'Goals & aspirations',
            self::Personality => 'Personality & preferences',
        };
    }
}
