<?php

declare(strict_types=1);

use App\Enums\RRuleFrequency;
use App\Models\Habit;

it('returns correct enum for daily on frequency attribute', function (): void {
    $habit = Habit::factory()->create(['rrule' => 'FREQ=DAILY']);

    expect($habit->frequency)->toBe(RRuleFrequency::Daily);
});

it('returns correct enum for weekly on frequency attribute', function (): void {
    $habit = Habit::factory()->create(['rrule' => 'FREQ=WEEKLY;BYDAY=MO,WE,FR']);

    expect($habit->frequency)->toBe(RRuleFrequency::Weekly);
});

it('returns null when rrule is null on frequency attribute', function (): void {
    $habit = Habit::factory()->create(['rrule' => null]);

    expect($habit->frequency)->toBeNull();
});

it('returns description for daily on human_readable attribute', function (): void {
    $habit = Habit::factory()->create(['rrule' => 'FREQ=DAILY']);

    expect($habit->human_readable)->toBeString()->not->toBeEmpty();
});

it('returns not set when rrule is null on human_readable attribute', function (): void {
    $habit = Habit::factory()->create(['rrule' => null]);

    expect($habit->human_readable)->toBeString();
});
