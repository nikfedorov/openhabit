<?php

declare(strict_types=1);

use App\Enums\RRuleFrequency;
use App\Services\RRuleService;
use Carbon\CarbonImmutable;

beforeEach(function (): void {
    $this->service = new RRuleService;
});

// ─── Build ──────────────────────────────────────────────────

it('builds daily rules', function (): void {
    expect($this->service->buildDaily())->toBe('FREQ=DAILY')
        ->and($this->service->buildDaily(2))->toBe('FREQ=DAILY;INTERVAL=2');
});

it('builds weekly rules', function (): void {
    expect($this->service->buildWeekly([0, 2, 4]))->toBe('FREQ=WEEKLY;BYDAY=MO,WE,FR')
        ->and($this->service->buildWeekly([0], 2))->toBe('FREQ=WEEKLY;INTERVAL=2;BYDAY=MO')
        ->and($this->service->buildWeekly([]))->toBe('FREQ=WEEKLY');
});

it('builds monthly rules', function (): void {
    expect($this->service->buildMonthlyByDay([1, 15]))->toBe('FREQ=MONTHLY;BYMONTHDAY=1,15')
        ->and($this->service->buildMonthlyByDay([]))->toBe('FREQ=MONTHLY')
        ->and($this->service->buildMonthlyByWeekday(1, 0))->toBe('FREQ=MONTHLY;BYDAY=1MO')
        ->and($this->service->buildMonthlyByWeekday(-1, 4))->toBe('FREQ=MONTHLY;BYDAY=-1FR')
        ->and($this->service->buildMonthlyByWeekday(1, 99))->toContain('BYDAY=1MO');
});

it('builds yearly rules', function (): void {
    $rrule = $this->service->buildYearly([1, 5, 10]);
    expect($rrule)->toContain('FREQ=YEARLY')
        ->toContain('BYWEEKNO=1,5,10')
        ->toContain('BYDAY=MO,TU,WE,TH,FR,SA,SU');

    $empty = $this->service->buildYearly([]);
    expect($empty)->toContain('FREQ=YEARLY')
        ->toContain('BYDAY=');
});

// ─── Parse ──────────────────────────────────────────────────

it('returns correct enum for getFrequency', function (string $rrule, RRuleFrequency $expected): void {
    expect($this->service->getFrequency($rrule))->toBe($expected);
})->with([
    ['FREQ=DAILY', RRuleFrequency::Daily],
    ['FREQ=WEEKLY;BYDAY=MO', RRuleFrequency::Weekly],
    ['FREQ=MONTHLY;BYMONTHDAY=1', RRuleFrequency::Monthly],
    ['FREQ=YEARLY;BYWEEKNO=1', RRuleFrequency::Yearly],
]);

it('returns null for invalid rrule in getFrequency', function (): void {
    expect($this->service->getFrequency('INVALID'))->toBeNull();
});

it('parses days of week correctly', function (): void {
    expect($this->service->parseDaysOfWeek('FREQ=WEEKLY;BYDAY=MO,WE,FR'))->toBe([0, 2, 4])
        ->and($this->service->parseDaysOfWeek('FREQ=DAILY'))->toBe([])
        ->and($this->service->parseDaysOfWeek('FREQ=MONTHLY;BYDAY=1MO'))->toBe([]);
});

it('parses ordinal weekday correctly', function (): void {
    $ordinal = $this->service->parseOrdinalWeekday('FREQ=MONTHLY;BYDAY=2TU');
    expect($ordinal->ordinal)->toBe(2)
        ->and($ordinal->day)->toBe(1);

    $simple = $this->service->parseOrdinalWeekday('FREQ=WEEKLY;BYDAY=MO');
    expect($simple->ordinal)->toBeNull()
        ->and($simple->day)->toBeNull();
});

it('parses days of month correctly', function (): void {
    expect($this->service->parseDaysOfMonth('FREQ=MONTHLY;BYMONTHDAY=1,15'))->toBe([1, 15])
        ->and($this->service->parseDaysOfMonth('FREQ=DAILY'))->toBe([]);
});

it('parses weeks of year correctly', function (): void {
    expect($this->service->parseWeeksOfYear('FREQ=YEARLY;BYWEEKNO=1,5,10'))->toBe([1, 5, 10])
        ->and($this->service->parseWeeksOfYear('FREQ=DAILY'))->toBe([]);
});

// ─── Match ──────────────────────────────────────────────────

it('matches dates by frequency type', function (): void {
    $monday = CarbonImmutable::parse('2025-01-06');
    $tuesday = CarbonImmutable::parse('2025-01-07');
    $jan15 = CarbonImmutable::parse('2025-01-15');

    // Daily always matches
    expect($this->service->matchesDate('FREQ=DAILY', $monday))->toBeTrue()
        // Weekly matches correct day, rejects wrong day
        ->and($this->service->matchesDate('FREQ=WEEKLY;BYDAY=MO', $monday))->toBeTrue()
        ->and($this->service->matchesDate('FREQ=WEEKLY;BYDAY=MO', $tuesday))->toBeFalse()
        // Monthly matches correct day of month
        ->and($this->service->matchesDate('FREQ=MONTHLY;BYMONTHDAY=15', $jan15))->toBeTrue()
        ->and($this->service->matchesDate('FREQ=MONTHLY;BYMONTHDAY=1', $jan15))->toBeFalse()
        // Invalid rule returns false
        ->and($this->service->matchesDate('INVALID_RULE', $monday))->toBeFalse();
});

it('caches matchesDate results and handles rrule library fallback', function (): void {
    $monday = CarbonImmutable::parse('2025-01-06');

    // Caching: same call twice returns same result
    expect($this->service->matchesDate('FREQ=DAILY', $monday))->toBeTrue()
        ->and($this->service->matchesDate('FREQ=DAILY', $monday))->toBeTrue()
        // Weekly with interval falls through to rrule library
        ->and($this->service->matchesDate('FREQ=WEEKLY;INTERVAL=2;BYDAY=MO', $monday))->toBeTrue()
        // Weekly without BYDAY falls through to rrule library
        ->and($this->service->matchesDate('FREQ=WEEKLY', $monday))->toBeBool();
});

// ─── Occurrences ────────────────────────────────────────────

it('returns occurrences between dates for different frequencies', function (): void {
    // Daily: 3 days
    expect($this->service->getOccurrencesBetween(
        'FREQ=DAILY',
        CarbonImmutable::parse('2025-01-01'),
        CarbonImmutable::parse('2025-01-03'),
    ))->toHaveCount(3)
        // Weekly: Mon + Fri in one week
        ->and($this->service->getOccurrencesBetween(
            'FREQ=WEEKLY;BYDAY=MO,FR',
            CarbonImmutable::parse('2025-01-06'),
            CarbonImmutable::parse('2025-01-12'),
        ))->toHaveCount(2)
        // Monthly: 15th in Jan, Feb, Mar
        ->and($this->service->getOccurrencesBetween(
            'FREQ=MONTHLY;BYMONTHDAY=15',
            CarbonImmutable::parse('2025-01-01'),
            CarbonImmutable::parse('2025-03-31'),
        ))->toHaveCount(3)
        // Invalid rule returns empty
        ->and($this->service->getOccurrencesBetween(
            'INVALID',
            CarbonImmutable::parse('2025-01-01'),
            CarbonImmutable::parse('2025-01-31'),
        ))->toBe([]);
});

// ─── Validation ─────────────────────────────────────────────

it('validates rrule strings', function (): void {
    expect($this->service->isValid('FREQ=DAILY'))->toBeTrue()
        ->and($this->service->isValid('INVALID'))->toBeFalse();
});

// ─── Description ────────────────────────────────────────────

it('returns non-empty descriptions for valid rrules', function (string $rrule): void {
    expect($this->service->getDescription($rrule))->toBeString()->not->toBeEmpty();
})->with([
    'FREQ=DAILY',
    'FREQ=DAILY;INTERVAL=3',
    'FREQ=WEEKLY;BYDAY=MO,TU,WE,TH,FR,SA,SU',
    'FREQ=WEEKLY;BYDAY=MO,TU,WE,TH,FR',
    'FREQ=WEEKLY;BYDAY=SA,SU',
    'FREQ=WEEKLY;BYDAY=MO,WE',
    'FREQ=WEEKLY',
    'FREQ=MONTHLY;BYMONTHDAY=1,15',
    'FREQ=MONTHLY;BYDAY=2TU',
    'FREQ=MONTHLY;BYDAY=-1FR',
    'FREQ=MONTHLY;BYDAY=5TU',
    'FREQ=MONTHLY',
    'FREQ=YEARLY;BYWEEKNO=1,5',
    'FREQ=YEARLY',
]);

it('returns raw string as description for invalid frequency', function (): void {
    expect($this->service->getDescription('SOMETHING=INVALID'))->toBe('SOMETHING=INVALID');
});
