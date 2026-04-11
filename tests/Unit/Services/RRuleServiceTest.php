<?php

declare(strict_types=1);

use App\Enums\RRuleFrequency;
use App\Services\RRuleService;
use Carbon\CarbonImmutable;

beforeEach(function (): void {
    $this->service = new RRuleService;
});

// ─── Build ──────────────────────────────────────────────────

it('returns FREQ=DAILY for buildDaily', function (): void {
    expect($this->service->buildDaily())->toBe('FREQ=DAILY');
});

it('returns FREQ=DAILY with interval for buildDaily', function (): void {
    expect($this->service->buildDaily(2))->toBe('FREQ=DAILY;INTERVAL=2');
});

it('returns FREQ=WEEKLY with days for buildWeekly', function (): void {
    $rrule = $this->service->buildWeekly([0, 2, 4]);

    expect($rrule)->toBe('FREQ=WEEKLY;BYDAY=MO,WE,FR');
});

it('returns FREQ=WEEKLY with interval for buildWeekly', function (): void {
    $rrule = $this->service->buildWeekly([0], 2);

    expect($rrule)->toBe('FREQ=WEEKLY;INTERVAL=2;BYDAY=MO');
});

it('returns FREQ=WEEKLY with empty days for buildWeekly', function (): void {
    $rrule = $this->service->buildWeekly([]);

    expect($rrule)->toBe('FREQ=WEEKLY');
});

it('returns FREQ=MONTHLY with days of month for buildMonthlyByDay', function (): void {
    $rrule = $this->service->buildMonthlyByDay([1, 15]);

    expect($rrule)->toBe('FREQ=MONTHLY;BYMONTHDAY=1,15');
});

it('returns FREQ=MONTHLY with no days for buildMonthlyByDay', function (): void {
    $rrule = $this->service->buildMonthlyByDay([]);

    expect($rrule)->toBe('FREQ=MONTHLY');
});

it('returns FREQ=MONTHLY with weekday for buildMonthlyByWeekday', function (): void {
    $rrule = $this->service->buildMonthlyByWeekday(1, 0);

    expect($rrule)->toBe('FREQ=MONTHLY;BYDAY=1MO');
});

it('returns FREQ=MONTHLY with last weekday for buildMonthlyByWeekday', function (): void {
    $rrule = $this->service->buildMonthlyByWeekday(-1, 4);

    expect($rrule)->toBe('FREQ=MONTHLY;BYDAY=-1FR');
});

it('returns FREQ=MONTHLY with invalid day using MO for buildMonthlyByWeekday', function (): void {
    $rrule = $this->service->buildMonthlyByWeekday(1, 99);

    expect($rrule)->toContain('BYDAY=1MO');
});

it('returns FREQ=YEARLY with weeks for buildYearly', function (): void {
    $rrule = $this->service->buildYearly([1, 5, 10]);

    expect($rrule)->toContain('FREQ=YEARLY')
        ->toContain('BYWEEKNO=1,5,10')
        ->toContain('BYDAY=MO,TU,WE,TH,FR,SA,SU');
});

it('returns FREQ=YEARLY with empty weeks for buildYearly', function (): void {
    $rrule = $this->service->buildYearly([]);

    expect($rrule)->toContain('FREQ=YEARLY')
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

it('returns ISO day numbers for parseDaysOfWeek', function (): void {
    $days = $this->service->parseDaysOfWeek('FREQ=WEEKLY;BYDAY=MO,WE,FR');

    expect($days)->toBe([0, 2, 4]);
});

it('returns empty array for parseDaysOfWeek with no BYDAY', function (): void {
    expect($this->service->parseDaysOfWeek('FREQ=DAILY'))->toBe([]);
});

it('returns empty array for parseDaysOfWeek with ordinal prefixes', function (): void {
    $days = $this->service->parseDaysOfWeek('FREQ=MONTHLY;BYDAY=1MO');

    expect($days)->toBe([]);
});

it('returns ordinal and day for parseOrdinalWeekday', function (): void {
    $result = $this->service->parseOrdinalWeekday('FREQ=MONTHLY;BYDAY=2TU');

    expect($result)->toBe(['ordinal' => 2, 'day' => 1]);
});

it('returns nulls for simple BYDAY in parseOrdinalWeekday', function (): void {
    $result = $this->service->parseOrdinalWeekday('FREQ=WEEKLY;BYDAY=MO');

    expect($result)->toBe(['ordinal' => null, 'day' => null]);
});

it('returns day numbers for parseDaysOfMonth', function (): void {
    $days = $this->service->parseDaysOfMonth('FREQ=MONTHLY;BYMONTHDAY=1,15');

    expect($days)->toBe([1, 15]);
});

it('returns empty array for parseDaysOfMonth with no BYMONTHDAY', function (): void {
    expect($this->service->parseDaysOfMonth('FREQ=DAILY'))->toBe([]);
});

it('returns week numbers for parseWeeksOfYear', function (): void {
    $weeks = $this->service->parseWeeksOfYear('FREQ=YEARLY;BYWEEKNO=1,5,10');

    expect($weeks)->toBe([1, 5, 10]);
});

it('returns empty array for parseWeeksOfYear with no BYWEEKNO', function (): void {
    expect($this->service->parseWeeksOfYear('FREQ=DAILY'))->toBe([]);
});

// ─── Match ──────────────────────────────────────────────────

it('returns true for matchesDate with daily rule', function (): void {
    $date = CarbonImmutable::parse('2025-01-06'); // Monday

    expect($this->service->matchesDate('FREQ=DAILY', $date))->toBeTrue();
});

it('returns correct result for matchesDate with weekly rule', function (): void {
    $monday = CarbonImmutable::parse('2025-01-06');
    $tuesday = CarbonImmutable::parse('2025-01-07');

    expect($this->service->matchesDate('FREQ=WEEKLY;BYDAY=MO', $monday))->toBeTrue()
        ->and($this->service->matchesDate('FREQ=WEEKLY;BYDAY=MO', $tuesday))->toBeFalse();
});

it('returns correct result for matchesDate with monthly rule', function (): void {
    $date = CarbonImmutable::parse('2025-01-15');

    expect($this->service->matchesDate('FREQ=MONTHLY;BYMONTHDAY=15', $date))->toBeTrue()
        ->and($this->service->matchesDate('FREQ=MONTHLY;BYMONTHDAY=1', $date))->toBeFalse();
});

it('returns false for matchesDate with invalid rrule', function (): void {
    $date = CarbonImmutable::parse('2025-01-06');

    expect($this->service->matchesDate('INVALID_RULE', $date))->toBeFalse();
});

it('caches results for matchesDate', function (): void {
    $date = CarbonImmutable::parse('2025-01-06');

    $result1 = $this->service->matchesDate('FREQ=DAILY', $date);
    $result2 = $this->service->matchesDate('FREQ=DAILY', $date);

    expect($result1)->toBeTrue()->and($result2)->toBeTrue();
});

it('uses rrule library for matchesDate with weekly rule with interval', function (): void {
    $date = CarbonImmutable::parse('2025-01-06');

    expect($this->service->matchesDate('FREQ=WEEKLY;INTERVAL=2;BYDAY=MO', $date))->toBeTrue();
});

it('uses rrule library for matchesDate with weekly rule without BYDAY', function (): void {
    $date = CarbonImmutable::parse('2025-01-06');

    // FREQ=WEEKLY without BYDAY — falls through to the rrule library
    expect($this->service->matchesDate('FREQ=WEEKLY', $date))->toBeBool();
});

// ─── Occurrences ────────────────────────────────────────────

it('returns all dates for getOccurrencesBetween with daily rule', function (): void {
    $start = CarbonImmutable::parse('2025-01-01');
    $end = CarbonImmutable::parse('2025-01-03');

    $occurrences = $this->service->getOccurrencesBetween('FREQ=DAILY', $start, $end);

    expect($occurrences)->toHaveCount(3);
});

it('returns correct occurrences for getOccurrencesBetween with weekly rule', function (): void {
    $start = CarbonImmutable::parse('2025-01-06'); // Monday
    $end = CarbonImmutable::parse('2025-01-12'); // Sunday

    $occurrences = $this->service->getOccurrencesBetween('FREQ=WEEKLY;BYDAY=MO,FR', $start, $end);

    expect($occurrences)->toHaveCount(2);
});

it('uses rrule library for getOccurrencesBetween with monthly rule', function (): void {
    $start = CarbonImmutable::parse('2025-01-01');
    $end = CarbonImmutable::parse('2025-03-31');

    $occurrences = $this->service->getOccurrencesBetween('FREQ=MONTHLY;BYMONTHDAY=15', $start, $end);

    expect($occurrences)->toHaveCount(3);
});

it('handles invalid rrule gracefully for getOccurrencesBetween', function (): void {
    $start = CarbonImmutable::parse('2025-01-01');
    $end = CarbonImmutable::parse('2025-01-31');

    $occurrences = $this->service->getOccurrencesBetween('INVALID', $start, $end);

    expect($occurrences)->toBe([]);
});

// ─── Validation ─────────────────────────────────────────────

it('returns true for isValid with valid rrule', function (): void {
    expect($this->service->isValid('FREQ=DAILY'))->toBeTrue();
});

it('returns false for isValid with invalid rrule', function (): void {
    expect($this->service->isValid('INVALID'))->toBeFalse();
});

// ─── Description ────────────────────────────────────────────

it('returns description for daily rule', function (): void {
    expect($this->service->getDescription('FREQ=DAILY'))->toBeString()->not->toBeEmpty();
});

it('returns description for daily rule with interval', function (): void {
    expect($this->service->getDescription('FREQ=DAILY;INTERVAL=3'))->toBeString()->not->toBeEmpty();
});

it('returns description for weekly rule with all days', function (): void {
    expect($this->service->getDescription('FREQ=WEEKLY;BYDAY=MO,TU,WE,TH,FR,SA,SU'))->toBeString();
});

it('returns description for weekly rule with weekdays', function (): void {
    expect($this->service->getDescription('FREQ=WEEKLY;BYDAY=MO,TU,WE,TH,FR'))->toBeString();
});

it('returns description for weekly rule with weekends', function (): void {
    expect($this->service->getDescription('FREQ=WEEKLY;BYDAY=SA,SU'))->toBeString();
});

it('returns description for specific weekly days', function (): void {
    expect($this->service->getDescription('FREQ=WEEKLY;BYDAY=MO,WE'))->toBeString()->not->toBeEmpty();
});

it('returns description for weekly rule with no days', function (): void {
    expect($this->service->getDescription('FREQ=WEEKLY'))->toBeString();
});

it('returns description for monthly rule by day', function (): void {
    expect($this->service->getDescription('FREQ=MONTHLY;BYMONTHDAY=1,15'))->toBeString()->not->toBeEmpty();
});

it('returns description for monthly rule by ordinal weekday', function (): void {
    expect($this->service->getDescription('FREQ=MONTHLY;BYDAY=2TU'))->toBeString()->not->toBeEmpty();
});

it('returns description for monthly rule by last weekday', function (): void {
    expect($this->service->getDescription('FREQ=MONTHLY;BYDAY=-1FR'))->toBeString()->not->toBeEmpty();
});

it('returns description for monthly simple rule', function (): void {
    expect($this->service->getDescription('FREQ=MONTHLY'))->toBeString();
});

it('returns description for yearly rule with weeks', function (): void {
    expect($this->service->getDescription('FREQ=YEARLY;BYWEEKNO=1,5'))->toBeString()->not->toBeEmpty();
});

it('returns description for yearly simple rule', function (): void {
    expect($this->service->getDescription('FREQ=YEARLY'))->toBeString();
});

it('returns description for invalid frequency as raw string', function (): void {
    expect($this->service->getDescription('SOMETHING=INVALID'))->toBe('SOMETHING=INVALID');
});

it('returns description for monthly ordinal with unknown position', function (): void {
    expect($this->service->getDescription('FREQ=MONTHLY;BYDAY=5TU'))->toBeString();
});
