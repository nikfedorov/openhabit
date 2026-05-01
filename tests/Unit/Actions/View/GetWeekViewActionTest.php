<?php

declare(strict_types=1);

use App\Actions\View\GetWeekViewAction;
use App\Models\AiDigest;
use App\Models\Habit;
use App\Models\User;

it('returns week data for the current week by default', function (): void {
    $user = User::factory()->create();
    Habit::factory()->daily()->for($user)->create();

    $data = resolve(GetWeekViewAction::class)->handle($user);

    expect($data->isCurrent)->toBeTrue()
        ->and($data->start)->toBe(now()->startOfWeek()->toDateString())
        ->and($data->end)->toBe(now()->endOfWeek()->toDateString())
        ->and($data->habits)->toHaveCount(1);
});

it('returns week data for a specific week and includes ai digests', function (): void {
    $user = User::factory()->create();
    $weekStart = now()->startOfWeek()->subWeek()->toDateString();

    AiDigest::factory()->for($user)->create([
        'date' => $weekStart,
        'content' => 'Weekly digest',
    ]);

    $data = resolve(GetWeekViewAction::class)->handle($user, $weekStart);

    expect($data->start)->toBe($weekStart)
        ->and($data->isCurrent)->toBeFalse()
        ->and($data->aiDigests)->toHaveCount(1)
        ->and($data->aiDigests->first()?->content)->toBe('Weekly digest');
});

it('defaults to previous calendar week when current time is before day_starts_at', function (): void {
    // 2 AM UTC on Monday; user's "today" is Sunday (previous calendar week)
    $this->travelTo(now()->startOfWeek()->setTime(2, 0, 0));
    $user = User::factory()->create(['timezone' => 'UTC', 'day_starts_at' => '03:00:00']);

    $data = resolve(GetWeekViewAction::class)->handle($user);

    // The user's current date is Sunday (previous week from calendar perspective)
    // isCurrent is true because this IS the user's current week
    expect($data->isCurrent)->toBeTrue()
        ->and($data->start)->toBe(now()->subWeek()->startOfWeek()->toDateString());
});
