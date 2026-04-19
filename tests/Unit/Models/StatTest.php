<?php

declare(strict_types=1);

use App\Enums\StatPeriod;
use App\Models\Stat;
use App\Models\User;

it('belongs to user', function (): void {
    $stat = Stat::factory()->create();

    expect($stat->user)->toBeInstanceOf(User::class);
});

it('returns correct levels', function (int $completed, int $total, int $expected): void {
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

it('returns completion rate as percentage', function (): void {
    $stat = Stat::factory()->create(['completed_count' => 5, 'planned_count' => 10]);

    expect($stat->completion_rate)->toBe(50.0);
});

it('returns zero for completion rate when total is zero', function (): void {
    $stat = Stat::factory()->create(['completed_count' => 0, 'planned_count' => 0]);

    expect($stat->completion_rate)->toBe(0.0);
});

it('returns intensity level computed from counts', function (): void {
    $stat = Stat::factory()->create(['completed_count' => 9, 'planned_count' => 10]);

    expect($stat->intensity_level)->toBe(4);
});

it('returns daily stats when filtered by daily scope', function (): void {
    Stat::factory()->create(['period' => StatPeriod::Daily]);
    Stat::factory()->create(['period' => StatPeriod::Weekly]);

    expect(Stat::query()->daily()->count())->toBe(1);
});

it('returns weekly stats when filtered by weekly scope', function (): void {
    Stat::factory()->create(['period' => StatPeriod::Weekly]);
    Stat::factory()->create(['period' => StatPeriod::Daily]);

    expect(Stat::query()->weekly()->count())->toBe(1);
});

it('returns yearly stats when filtered by yearly scope', function (): void {
    Stat::factory()->create(['period' => StatPeriod::Yearly]);
    Stat::factory()->create(['period' => StatPeriod::Daily]);

    expect(Stat::query()->yearly()->count())->toBe(1);
});

it('has correct casts', function (): void {
    $stat = Stat::factory()->create();

    expect($stat->id)->toBeInt()
        ->and($stat->user_id)->toBeInt()
        ->and($stat->period)->toBeInstanceOf(StatPeriod::class);
});
