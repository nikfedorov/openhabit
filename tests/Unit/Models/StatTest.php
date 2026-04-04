<?php

declare(strict_types=1);

use App\Enums\StatPeriod;
use App\Models\Stat;
use App\Models\User;

test('belongs to user', function (): void {
    $stat = Stat::factory()->create();

    expect($stat->user)->toBeInstanceOf(User::class);
});

test('calculateIntensity returns correct levels', function (int $completed, int $total, int $expected): void {
    expect(Stat::calculateIntensity($completed, $total))->toBe($expected);
})->with([
    [0, 0, 0],
    [0, 10, 0],
    [1, 10, 1],
    [3, 10, 2],
    [5, 10, 3],
    [8, 10, 4],
    [10, 10, 4],
]);

test('completion_rate attribute returns percentage', function (): void {
    $stat = Stat::factory()->create(['completed_count' => 5, 'planned_count' => 10]);

    expect($stat->completion_rate)->toBe(50.0);
});

test('completion_rate returns zero when total is zero', function (): void {
    $stat = Stat::factory()->create(['completed_count' => 0, 'planned_count' => 0]);

    expect($stat->completion_rate)->toBe(0.0);
});

test('intensity_level attribute computes from counts', function (): void {
    $stat = Stat::factory()->create(['completed_count' => 9, 'planned_count' => 10]);

    expect($stat->intensity_level)->toBe(4);
});

test('daily scope filters by period', function (): void {
    Stat::factory()->create(['period' => StatPeriod::Daily]);
    Stat::factory()->create(['period' => StatPeriod::Weekly]);

    expect(Stat::query()->daily()->count())->toBe(1);
});

test('weekly scope filters by period', function (): void {
    Stat::factory()->create(['period' => StatPeriod::Weekly]);
    Stat::factory()->create(['period' => StatPeriod::Daily]);

    expect(Stat::query()->weekly()->count())->toBe(1);
});

test('yearly scope filters by period', function (): void {
    Stat::factory()->create(['period' => StatPeriod::Yearly]);
    Stat::factory()->create(['period' => StatPeriod::Daily]);

    expect(Stat::query()->yearly()->count())->toBe(1);
});

test('casts are correct', function (): void {
    $stat = Stat::factory()->create();

    expect($stat->id)->toBeInt()
        ->and($stat->user_id)->toBeString()
        ->and($stat->period)->toBeInstanceOf(StatPeriod::class);
});
