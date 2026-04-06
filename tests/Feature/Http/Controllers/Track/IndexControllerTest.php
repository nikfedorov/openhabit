<?php

declare(strict_types=1);

use App\Models\DailyNote;
use App\Models\Habit;
use App\Models\HabitCompletion;
use App\Models\Stat;
use App\Models\User;

test('track api requires authentication', function (): void {
    $this->getJson('/api/track')
        ->assertUnauthorized();
});

test('track api loads habits with completion state and filters inactive ones', function (): void {
    $user = User::factory()->create();
    $habit = Habit::factory()->daily()->create(['user_id' => $user->id]);
    Habit::factory()->daily()->create(['user_id' => $user->id, 'is_active' => false]);

    HabitCompletion::factory()->create([
        'habit_id' => $habit->id,
        'user_id' => $user->id,
        'completed_at' => now()->toDateString(),
        'current_iteration' => 1,
    ]);

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/track')
        ->assertOk()
        ->assertJson([
            'isToday' => true,
            'totalHabits' => 1,
            'completedCount' => 1,
        ])
        ->assertJsonCount(1, 'habits')
        ->assertJsonPath('habits.0.is_completed', true)
        ->assertJsonPath('habits.0.current_iteration', 1)
        ->assertJsonPath('habits.0.sort_order', $habit->sort_order)
        ->assertJsonStructure(['translations', 'moveCompletedToEnd']);
});

test('track api supports date navigation and clamps future dates', function (): void {
    $user = User::factory()->create();
    Habit::factory()->daily()->create(['user_id' => $user->id]);

    $yesterday = now()->subDay()->toDateString();
    $tomorrow = now()->addDay()->toDateString();
    $today = now()->toDateString();

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/track?date='.$yesterday)
        ->assertOk()
        ->assertJson([
            'date' => $yesterday,
            'isToday' => false,
        ]);

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/track?date='.$tomorrow)
        ->assertOk()
        ->assertJson([
            'date' => $today,
            'isToday' => true,
        ]);
});

test('track api includes habits with null rrule alongside daily habits', function (): void {
    $user = User::factory()->create();
    Habit::factory()->daily()->create(['user_id' => $user->id]);
    Habit::factory()->create(['user_id' => $user->id, 'rrule' => null]);

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/track')
        ->assertOk()
        ->assertJsonCount(2, 'habits');
});

test('track api preserves sort order when move_completed_to_end is disabled', function (): void {
    $user = User::factory()->create(['move_completed_to_end' => false]);
    $habit1 = Habit::factory()->daily()->create(['user_id' => $user->id, 'sort_order' => 1]);
    $habit2 = Habit::factory()->daily()->create(['user_id' => $user->id, 'sort_order' => 2]);

    HabitCompletion::factory()->create([
        'habit_id' => $habit1->id,
        'user_id' => $user->id,
        'completed_at' => now()->toDateString(),
        'current_iteration' => 1,
    ]);

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/track')
        ->assertOk()
        ->assertJsonPath('habits.0.id', $habit1->id)
        ->assertJsonPath('habits.1.id', $habit2->id);
});

test('track api includes daily note and activity data', function (): void {
    $user = User::factory()->create();

    DailyNote::factory()->create([
        'user_id' => $user->id,
        'date' => now()->toDateString(),
        'content' => 'My note',
    ]);

    Stat::factory()->daily()->create([
        'user_id' => $user->id,
        'period_start' => now()->toDateString(),
        'planned_count' => 5,
        'completed_count' => 3,
    ]);

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/track')
        ->assertOk()
        ->assertJsonPath('dailyNoteContent', 'My note')
        ->assertJsonCount(1, 'activityData')
        ->assertJsonPath('activityData.0.completed', 3)
        ->assertJsonPath('activityData.0.total', 5);
});

test('track api uses user locale for translations', function (): void {
    $user = User::factory()->create(['locale' => 'ru']);

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/track')
        ->assertOk()
        ->assertJsonPath('translations.progress', 'Прогресс')
        ->assertJsonPath('translations.today', 'Сегодня');
});
