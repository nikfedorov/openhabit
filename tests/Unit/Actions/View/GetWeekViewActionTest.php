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

it('does not mark today as future when week param equals current week start in a UTC+ timezone', function (): void {
    // User is in UTC+3 (Moscow). 10:00 Moscow = 07:00 UTC.
    // The week start date string (Monday 2026-05-18) parsed in UTC gives
    // 2026-05-18 00:00 UTC, which is 3h *after* the user's today (2026-05-17 21:00 UTC).
    // Without the fix this caused today to be flagged as is_future.
    $this->travelTo('2026-05-18 07:00:00'); // 10:00 Moscow = 07:00 UTC
    $user = User::factory()->create(['timezone' => 'Europe/Moscow']);
    Habit::factory()->daily()->for($user)->create();

    // Pass today's Monday date explicitly, as the browser does when navigating back
    $data = resolve(GetWeekViewAction::class)->handle($user, '2026-05-18');

    $todayDay = collect($data->days)->firstWhere('date', '2026-05-18');

    expect($todayDay->isToday)->toBeTrue()
        ->and($todayDay->isFuture)->toBeFalse();
});
