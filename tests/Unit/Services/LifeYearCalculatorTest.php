<?php

declare(strict_types=1);

use App\Services\LifeYearCalculator;
use Carbon\CarbonImmutable;

beforeEach(function (): void {
    $this->calculator = new LifeYearCalculator;
});

it('returns Monday on or after birthday', function (): void {
    // 2000-06-15 is Thursday
    $birthdate = CarbonImmutable::parse('2000-06-15');

    $year0Start = $this->calculator->getYearStart($birthdate, 0);

    expect($year0Start->dayOfWeek)->toBe(CarbonImmutable::MONDAY)
        ->and($year0Start->gte($birthdate))->toBeTrue();
});

it('returns birthday when it falls on Monday', function (): void {
    // 2024-01-01 is Monday
    $birthdate = CarbonImmutable::parse('2024-01-01');

    $year0Start = $this->calculator->getYearStart($birthdate, 0);

    expect($year0Start->toDateString())->toBe('2024-01-01');
});

it('correctly calculates year 1', function (): void {
    $birthdate = CarbonImmutable::parse('2000-06-15');

    $year1Start = $this->calculator->getYearStart($birthdate, 1);

    expect($year1Start->year)->toBe(2001)
        ->and($year1Start->dayOfWeek)->toBe(CarbonImmutable::MONDAY);
});

it('returns correct life year for a given date', function (): void {
    $birthdate = CarbonImmutable::parse('2000-01-01');
    $date = CarbonImmutable::parse('2025-06-15');

    $year = $this->calculator->getYearForDate($date, $birthdate);

    expect($year)->toBe(25);
});

it('returns 0 for date near birth', function (): void {
    $birthdate = CarbonImmutable::parse('2000-01-03'); // Monday
    $date = CarbonImmutable::parse('2000-06-15');

    $year = $this->calculator->getYearForDate($date, $birthdate);

    expect($year)->toBe(0);
});

it('returns integer age for current age', function (): void {
    $birthdate = CarbonImmutable::parse('2000-01-01');

    $age = $this->calculator->getCurrentAge($birthdate);

    expect($age)->toBeInt()->toBeGreaterThan(0);
});

it('returns integer weeks for weeks lived', function (): void {
    $birthdate = CarbonImmutable::parse('2000-01-01');

    $weeks = $this->calculator->getWeeksLived($birthdate);

    expect($weeks)->toBeInt()->toBeGreaterThan(0);
});

it('returns 0 for date before birth', function (): void {
    $birthdate = CarbonImmutable::parse('2000-06-15');
    $date = CarbonImmutable::parse('1999-01-01');

    $year = $this->calculator->getYearForDate($date, $birthdate);

    expect($year)->toBe(0);
});
