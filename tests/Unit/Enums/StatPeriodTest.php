<?php

declare(strict_types=1);

use App\Enums\StatPeriod;

it('has expected cases', function (): void {
    expect(StatPeriod::cases())->toHaveCount(3)
        ->and(StatPeriod::Daily->value)->toBe('daily')
        ->and(StatPeriod::Weekly->value)->toBe('weekly')
        ->and(StatPeriod::Yearly->value)->toBe('yearly');
});
