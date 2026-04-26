<?php

declare(strict_types=1);

use App\Actions\Track\GetTrackDataAction;
use App\Models\AiDigest;
use App\Models\DailyNote;
use App\Models\Habit;
use App\Models\HabitCompletion;
use App\Models\Stat;
use App\Models\User;

it('returns track data for today, including completion state and filters out inactive habits', function (): void {
    $user = User::factory()->create();
    $habit = Habit::factory()->daily()->for($user)->create(['iterations_required' => 1]);
    Habit::factory()->daily()->for($user)->create(['is_active' => false]);

    HabitCompletion::factory()->for($habit)->for($user)->create([
        'completed_at' => now()->toDateString(),
        'current_iteration' => 1,
    ]);

    $data = resolve(GetTrackDataAction::class)->handle($user);

    expect($data->date)->toBe(now()->toDateString())
        ->and($data->isToday)->toBeTrue()
        ->and($data->totalHabits)->toBe(1)
        ->and($data->completedCount)->toBe(1)
        ->and($data->habits[0]->getAttribute('is_completed'))->toBeTrue();
});

it('clamps future dates to today and parses provided dates', function (): void {
    $user = User::factory()->create();

    $future = resolve(GetTrackDataAction::class)->handle($user, now()->addDay()->toDateString());
    $past = resolve(GetTrackDataAction::class)->handle($user, now()->subDay()->toDateString());

    expect($future->date)->toBe(now()->toDateString())
        ->and($future->isToday)->toBeTrue()
        ->and($past->date)->toBe(now()->subDay()->toDateString())
        ->and($past->isToday)->toBeFalse();
});

it('includes habits with null rrule and respects rrule matching', function (): void {
    $user = User::factory()->create();
    Habit::factory()->daily()->for($user)->create();
    Habit::factory()->for($user)->create(['rrule' => null]);
    // weekly that doesn't match today
    $offDay = now()->addDay()->englishDayOfWeek;
    Habit::factory()->for($user)->create(['rrule' => 'FREQ=WEEKLY;BYDAY='.mb_strtoupper(mb_substr($offDay, 0, 2))]);

    $data = resolve(GetTrackDataAction::class)->handle($user);

    expect($data->totalHabits)->toBe(2);
});

it('moves completed habits to the end when the user setting is enabled', function (): void {
    $user = User::factory()->create(['move_completed_to_end' => true]);
    $first = Habit::factory()->daily()->for($user)->create(['sort_order' => 1, 'iterations_required' => 1]);
    $second = Habit::factory()->daily()->for($user)->create(['sort_order' => 2, 'iterations_required' => 1]);

    HabitCompletion::factory()->for($first)->for($user)->create([
        'completed_at' => now()->toDateString(),
        'current_iteration' => 1,
    ]);

    $data = resolve(GetTrackDataAction::class)->handle($user);

    expect($data->habits->pluck('id')->all())->toBe([$second->id, $first->id]);
});

it('preserves sort order when move_completed_to_end is disabled', function (): void {
    $user = User::factory()->create(['move_completed_to_end' => false]);
    $first = Habit::factory()->daily()->for($user)->create(['sort_order' => 1, 'iterations_required' => 1]);
    $second = Habit::factory()->daily()->for($user)->create(['sort_order' => 2]);

    HabitCompletion::factory()->for($first)->for($user)->create([
        'completed_at' => now()->toDateString(),
        'current_iteration' => 1,
    ]);

    $data = resolve(GetTrackDataAction::class)->handle($user);

    expect($data->habits->pluck('id')->all())->toBe([$first->id, $second->id]);
});

it('includes daily note, activity data and ai digest', function (): void {
    $user = User::factory()->create();
    $today = now()->toDateString();

    DailyNote::factory()->for($user)->create(['date' => $today, 'content' => 'My note']);
    Stat::factory()->daily()->for($user)->create([
        'period_start' => $today,
        'planned_count' => 5,
        'completed_count' => 3,
    ]);
    AiDigest::factory()->for($user)->create(['date' => $today, 'content' => 'digest']);

    $data = resolve(GetTrackDataAction::class)->handle($user);

    expect($data->dailyNoteContent)->toBe('My note')
        ->and($data->activityData)->toHaveCount(1)
        ->and($data->activityData[0]->completed)->toBe(3)
        ->and($data->activityData[0]->total)->toBe(5)
        ->and($data->aiDigest?->content)->toBe('digest')
        ->and($data->translations)->toHaveKey('last_n_days');
});
