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
