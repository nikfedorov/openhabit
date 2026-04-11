<?php

declare(strict_types=1);

use App\Models\DailyNote;
use App\Models\Habit;
use App\Models\HabitCompletion;
use App\Models\Stat;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;

it('requires authentication', function (): void {
    $this->getJson('/api/track')
        ->assertUnauthorized();
});

it('loads habits with completion state and filters inactive ones', function (): void {
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
        ->assertJson(fn (AssertableJson $json): AssertableJson => $json->has('navigationTranslations')
            ->has('settings', fn (AssertableJson $json): AssertableJson => $json->has('locale')
                ->has('theme')
                ->has('moveCompletedToEnd')
            )
            ->has('habits', 1, fn (AssertableJson $json): AssertableJson => $json->where('is_completed', true)
                ->where('current_iteration', 1)
                ->where('sort_order', $habit->sort_order)
                ->has('id')
                ->has('name')
                ->has('description')
                ->has('iterations_required')
            )
            ->has('activityData')
            ->has('data', fn (AssertableJson $json): AssertableJson => $json->where('isToday', true)
                ->where('totalHabits', 1)
                ->where('completedCount', 1)
                ->has('date')
                ->has('dayName')
                ->has('dateFormatted')
                ->has('dailyNoteContent')
                ->has('translations')
            )
        );
});

it('supports date navigation and clamps future dates', function (): void {
    $user = User::factory()->create();
    Habit::factory()->daily()->create(['user_id' => $user->id]);

    $yesterday = now()->subDay()->toDateString();
    $tomorrow = now()->addDay()->toDateString();
    $today = now()->toDateString();

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/track?date='.$yesterday)
        ->assertOk()
        ->assertJson(fn (AssertableJson $json): AssertableJson => $json->has('data', fn (AssertableJson $json): AssertableJson => $json->where('date', $yesterday)
            ->where('isToday', false)
            ->etc()
        )
            ->etc()
        );

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/track?date='.$tomorrow)
        ->assertOk()
        ->assertJson(fn (AssertableJson $json): AssertableJson => $json->has('data', fn (AssertableJson $json): AssertableJson => $json->where('date', $today)
            ->where('isToday', true)
            ->etc()
        )
            ->etc()
        );
});

it('includes habits with null rrule alongside daily habits', function (): void {
    $user = User::factory()->create();
    Habit::factory()->daily()->create(['user_id' => $user->id]);
    Habit::factory()->create(['user_id' => $user->id, 'rrule' => null]);

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/track')
        ->assertOk()
        ->assertJson(fn (AssertableJson $json): AssertableJson => $json->has('habits', 2)
            ->has('data', fn (AssertableJson $json): AssertableJson => $json->etc())
            ->etc()
        );
});

it('preserves sort order when move_completed_to_end is disabled', function (): void {
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
        ->assertJson(fn (AssertableJson $json): AssertableJson => $json->has('habits', 2)
            ->where('habits.0.id', $habit1->id)
            ->where('habits.1.id', $habit2->id)
            ->has('data', fn (AssertableJson $json): AssertableJson => $json->etc())
            ->etc()
        );
});

it('includes daily note and activity data', function (): void {
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
        ->assertJson(fn (AssertableJson $json): AssertableJson => $json->has('activityData', 1, fn (AssertableJson $json): AssertableJson => $json->where('completed', 3)
            ->where('total', 5)
            ->etc()
        )
            ->has('data', fn (AssertableJson $json): AssertableJson => $json->where('dailyNoteContent', 'My note')
            ->etc()
            )
            ->etc()
        );
});

it('uses user locale for translations', function (): void {
    $user = User::factory()->create(['locale' => 'ru']);

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/track')
        ->assertOk()
        ->assertJson(fn (AssertableJson $json): AssertableJson => $json->has('data', fn (AssertableJson $json): AssertableJson => $json->has('translations', fn (AssertableJson $json): AssertableJson => $json->where('progress', 'Прогресс')
            ->where('today', 'Сегодня')
            ->etc()
        )
            ->etc()
        )
            ->etc()
        );
});
