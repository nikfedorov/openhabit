<?php

declare(strict_types=1);

use App\Enums\RRuleFrequency;
use App\Models\Habit;

test('frequency attribute returns correct enum for daily', function (): void {
    $habit = Habit::factory()->create(['rrule' => 'FREQ=DAILY']);

    expect($habit->frequency)->toBe(RRuleFrequency::Daily);
});

test('frequency attribute returns correct enum for weekly', function (): void {
    $habit = Habit::factory()->create(['rrule' => 'FREQ=WEEKLY;BYDAY=MO,WE,FR']);

    expect($habit->frequency)->toBe(RRuleFrequency::Weekly);
});

test('frequency attribute returns null when rrule is null', function (): void {
    $habit = Habit::factory()->create(['rrule' => null]);

    expect($habit->frequency)->toBeNull();
});

test('human_readable attribute returns description for daily', function (): void {
    $habit = Habit::factory()->create(['rrule' => 'FREQ=DAILY']);

    expect($habit->human_readable)->toBeString()->not->toBeEmpty();
});

test('human_readable attribute returns not set when rrule is null', function (): void {
    $habit = Habit::factory()->create(['rrule' => null]);

    expect($habit->human_readable)->toBeString();
});
