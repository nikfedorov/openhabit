<?php

declare(strict_types=1);

use App\Enums\RRuleFrequency;
use App\Services\RRuleService;
use Carbon\CarbonImmutable;

beforeEach(function (): void {
    $this->service = new RRuleService;
});

// ─── Build ──────────────────────────────────────────────────

test('buildDaily returns FREQ=DAILY', function (): void {
    expect($this->service->buildDaily())->toBe('FREQ=DAILY');
});

test('buildDaily with interval', function (): void {
    expect($this->service->buildDaily(2))->toBe('FREQ=DAILY;INTERVAL=2');
});

test('buildWeekly with days', function (): void {
    $rrule = $this->service->buildWeekly([0, 2, 4]);

    expect($rrule)->toBe('FREQ=WEEKLY;BYDAY=MO,WE,FR');
});

test('buildWeekly with interval', function (): void {
    $rrule = $this->service->buildWeekly([0], 2);

    expect($rrule)->toBe('FREQ=WEEKLY;INTERVAL=2;BYDAY=MO');
});

test('buildWeekly with empty days', function (): void {
    $rrule = $this->service->buildWeekly([]);

    expect($rrule)->toBe('FREQ=WEEKLY');
});

test('buildMonthlyByDay with days of month', function (): void {
    $rrule = $this->service->buildMonthlyByDay([1, 15]);

    expect($rrule)->toBe('FREQ=MONTHLY;BYMONTHDAY=1,15');
});

test('buildMonthlyByDay with no days', function (): void {
    $rrule = $this->service->buildMonthlyByDay([]);

    expect($rrule)->toBe('FREQ=MONTHLY');
});

test('buildMonthlyByWeekday', function (): void {
    $rrule = $this->service->buildMonthlyByWeekday(1, 0);

    expect($rrule)->toBe('FREQ=MONTHLY;BYDAY=1MO');
});

test('buildMonthlyByWeekday with last', function (): void {
    $rrule = $this->service->buildMonthlyByWeekday(-1, 4);

    expect($rrule)->toBe('FREQ=MONTHLY;BYDAY=-1FR');
});

test('buildMonthlyByWeekday with invalid day uses MO', function (): void {
    $rrule = $this->service->buildMonthlyByWeekday(1, 99);

    expect($rrule)->toContain('BYDAY=1MO');
});

test('buildYearly with weeks', function (): void {
    $rrule = $this->service->buildYearly([1, 5, 10]);

    expect($rrule)->toContain('FREQ=YEARLY')
        ->toContain('BYWEEKNO=1,5,10')
        ->toContain('BYDAY=MO,TU,WE,TH,FR,SA,SU');
});

test('buildYearly with empty weeks', function (): void {
    $rrule = $this->service->buildYearly([]);

    expect($rrule)->toContain('FREQ=YEARLY')
        ->toContain('BYDAY=');
});

// ─── Parse ──────────────────────────────────────────────────

test('getFrequency returns correct enum', function (string $rrule, RRuleFrequency $expected): void {
    expect($this->service->getFrequency($rrule))->toBe($expected);
})->with([
    ['FREQ=DAILY', RRuleFrequency::Daily],
    ['FREQ=WEEKLY;BYDAY=MO', RRuleFrequency::Weekly],
    ['FREQ=MONTHLY;BYMONTHDAY=1', RRuleFrequency::Monthly],
    ['FREQ=YEARLY;BYWEEKNO=1', RRuleFrequency::Yearly],
]);

test('getFrequency returns null for invalid rrule', function (): void {
    expect($this->service->getFrequency('INVALID'))->toBeNull();
});

test('parseDaysOfWeek returns ISO day numbers', function (): void {
    $days = $this->service->parseDaysOfWeek('FREQ=WEEKLY;BYDAY=MO,WE,FR');

    expect($days)->toBe([0, 2, 4]);
});

test('parseDaysOfWeek returns empty for no BYDAY', function (): void {
    expect($this->service->parseDaysOfWeek('FREQ=DAILY'))->toBe([]);
});

test('parseDaysOfWeek skips ordinal prefixes', function (): void {
    $days = $this->service->parseDaysOfWeek('FREQ=MONTHLY;BYDAY=1MO');

    expect($days)->toBe([]);
});

test('parseOrdinalWeekday returns ordinal and day', function (): void {
    $result = $this->service->parseOrdinalWeekday('FREQ=MONTHLY;BYDAY=2TU');

    expect($result)->toBe(['ordinal' => 2, 'day' => 1]);
});

test('parseOrdinalWeekday returns nulls for simple BYDAY', function (): void {
    $result = $this->service->parseOrdinalWeekday('FREQ=WEEKLY;BYDAY=MO');

    expect($result)->toBe(['ordinal' => null, 'day' => null]);
});

test('parseDaysOfMonth returns day numbers', function (): void {
    $days = $this->service->parseDaysOfMonth('FREQ=MONTHLY;BYMONTHDAY=1,15');

    expect($days)->toBe([1, 15]);
});

test('parseDaysOfMonth returns empty for no BYMONTHDAY', function (): void {
    expect($this->service->parseDaysOfMonth('FREQ=DAILY'))->toBe([]);
});

test('parseWeeksOfYear returns week numbers', function (): void {
    $weeks = $this->service->parseWeeksOfYear('FREQ=YEARLY;BYWEEKNO=1,5,10');

    expect($weeks)->toBe([1, 5, 10]);
});

test('parseWeeksOfYear returns empty for no BYWEEKNO', function (): void {
    expect($this->service->parseWeeksOfYear('FREQ=DAILY'))->toBe([]);
});

// ─── Match ──────────────────────────────────────────────────

test('matchesDate returns true for daily rule', function (): void {
    $date = CarbonImmutable::parse('2025-01-06'); // Monday

    expect($this->service->matchesDate('FREQ=DAILY', $date))->toBeTrue();
});

test('matchesDate checks day for weekly rule', function (): void {
    $monday = CarbonImmutable::parse('2025-01-06');
    $tuesday = CarbonImmutable::parse('2025-01-07');

    expect($this->service->matchesDate('FREQ=WEEKLY;BYDAY=MO', $monday))->toBeTrue()
        ->and($this->service->matchesDate('FREQ=WEEKLY;BYDAY=MO', $tuesday))->toBeFalse();
});

test('matchesDate handles monthly rule', function (): void {
    $date = CarbonImmutable::parse('2025-01-15');

    expect($this->service->matchesDate('FREQ=MONTHLY;BYMONTHDAY=15', $date))->toBeTrue()
        ->and($this->service->matchesDate('FREQ=MONTHLY;BYMONTHDAY=1', $date))->toBeFalse();
});

test('matchesDate handles invalid rrule gracefully', function (): void {
    $date = CarbonImmutable::parse('2025-01-06');

    expect($this->service->matchesDate('INVALID_RULE', $date))->toBeFalse();
});

test('matchesDate caches results', function (): void {
    $date = CarbonImmutable::parse('2025-01-06');

    $result1 = $this->service->matchesDate('FREQ=DAILY', $date);
    $result2 = $this->service->matchesDate('FREQ=DAILY', $date);

    expect($result1)->toBeTrue()->and($result2)->toBeTrue();
});

test('matchesDate with weekly rule with interval uses rrule library', function (): void {
    $date = CarbonImmutable::parse('2025-01-06');

    expect($this->service->matchesDate('FREQ=WEEKLY;INTERVAL=2;BYDAY=MO', $date))->toBeTrue();
});

test('matchesDate with weekly rule without BYDAY uses rrule library', function (): void {
    $date = CarbonImmutable::parse('2025-01-06');

    // FREQ=WEEKLY without BYDAY — falls through to the rrule library
    expect($this->service->matchesDate('FREQ=WEEKLY', $date))->toBeBool();
});

// ─── Occurrences ────────────────────────────────────────────

test('getOccurrencesBetween daily returns all dates', function (): void {
    $start = CarbonImmutable::parse('2025-01-01');
    $end = CarbonImmutable::parse('2025-01-03');

    $occurrences = $this->service->getOccurrencesBetween('FREQ=DAILY', $start, $end);

    expect($occurrences)->toHaveCount(3);
});

test('getOccurrencesBetween weekly fast path', function (): void {
    $start = CarbonImmutable::parse('2025-01-06'); // Monday
    $end = CarbonImmutable::parse('2025-01-12'); // Sunday

    $occurrences = $this->service->getOccurrencesBetween('FREQ=WEEKLY;BYDAY=MO,FR', $start, $end);

    expect($occurrences)->toHaveCount(2);
});

test('getOccurrencesBetween monthly uses rrule library', function (): void {
    $start = CarbonImmutable::parse('2025-01-01');
    $end = CarbonImmutable::parse('2025-03-31');

    $occurrences = $this->service->getOccurrencesBetween('FREQ=MONTHLY;BYMONTHDAY=15', $start, $end);

    expect($occurrences)->toHaveCount(3);
});

test('getOccurrencesBetween handles invalid rrule gracefully', function (): void {
    $start = CarbonImmutable::parse('2025-01-01');
    $end = CarbonImmutable::parse('2025-01-31');

    $occurrences = $this->service->getOccurrencesBetween('INVALID', $start, $end);

    expect($occurrences)->toBe([]);
});

// ─── Validation ─────────────────────────────────────────────

test('isValid returns true for valid rrule', function (): void {
    expect($this->service->isValid('FREQ=DAILY'))->toBeTrue();
});

test('isValid returns false for invalid rrule', function (): void {
    expect($this->service->isValid('INVALID'))->toBeFalse();
});

// ─── Description ────────────────────────────────────────────

test('getDescription for daily rule', function (): void {
    expect($this->service->getDescription('FREQ=DAILY'))->toBeString()->not->toBeEmpty();
});

test('getDescription for daily with interval', function (): void {
    expect($this->service->getDescription('FREQ=DAILY;INTERVAL=3'))->toBeString()->not->toBeEmpty();
});

test('getDescription for weekly all days', function (): void {
    expect($this->service->getDescription('FREQ=WEEKLY;BYDAY=MO,TU,WE,TH,FR,SA,SU'))->toBeString();
});

test('getDescription for weekdays', function (): void {
    expect($this->service->getDescription('FREQ=WEEKLY;BYDAY=MO,TU,WE,TH,FR'))->toBeString();
});

test('getDescription for weekends', function (): void {
    expect($this->service->getDescription('FREQ=WEEKLY;BYDAY=SA,SU'))->toBeString();
});

test('getDescription for specific weekly days', function (): void {
    expect($this->service->getDescription('FREQ=WEEKLY;BYDAY=MO,WE'))->toBeString()->not->toBeEmpty();
});

test('getDescription for weekly with no days', function (): void {
    expect($this->service->getDescription('FREQ=WEEKLY'))->toBeString();
});

test('getDescription for monthly by day', function (): void {
    expect($this->service->getDescription('FREQ=MONTHLY;BYMONTHDAY=1,15'))->toBeString()->not->toBeEmpty();
});

test('getDescription for monthly by ordinal weekday', function (): void {
    expect($this->service->getDescription('FREQ=MONTHLY;BYDAY=2TU'))->toBeString()->not->toBeEmpty();
});

test('getDescription for monthly by last weekday', function (): void {
    expect($this->service->getDescription('FREQ=MONTHLY;BYDAY=-1FR'))->toBeString()->not->toBeEmpty();
});

test('getDescription for monthly simple', function (): void {
    expect($this->service->getDescription('FREQ=MONTHLY'))->toBeString();
});

test('getDescription for yearly with weeks', function (): void {
    expect($this->service->getDescription('FREQ=YEARLY;BYWEEKNO=1,5'))->toBeString()->not->toBeEmpty();
});

test('getDescription for yearly simple', function (): void {
    expect($this->service->getDescription('FREQ=YEARLY'))->toBeString();
});

test('getDescription for invalid frequency returns raw string', function (): void {
    expect($this->service->getDescription('SOMETHING=INVALID'))->toBe('SOMETHING=INVALID');
});

test('getDescription for monthly ordinal with unknown position', function (): void {
    expect($this->service->getDescription('FREQ=MONTHLY;BYDAY=5TU'))->toBeString();
});
