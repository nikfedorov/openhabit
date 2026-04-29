<?php

declare(strict_types=1);

use App\Models\AiDigest;
use App\Models\Habit;
use App\Models\HabitCompletion;
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

    AiDigest::factory()->for($user)->create(['date' => now()->toDateString()]);

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/track')
        ->assertOk()
        ->assertJson(fn (AssertableJson $json): AssertableJson => $json
            ->has('navigationTranslations')
            ->has('settings', fn (AssertableJson $json): AssertableJson => $json->has('locale')
                ->has('theme')
                ->has('moveCompletedToEnd')
                ->etc()
            )
            ->has('habits', 1, fn (AssertableJson $json): AssertableJson => $json
                ->where('is_completed', true)
                ->where('current_iteration', 1)
                ->where('sort_order', $habit->sort_order)
                ->has('id')
                ->has('name')
                ->has('description')
                ->has('iterations_required')
            )
            ->has('activityData')
            ->has('data', fn (AssertableJson $json): AssertableJson => $json
                ->where('isToday', true)
                ->where('totalHabits', 1)
                ->where('completedCount', 1)
                ->has('date')
                ->has('dayName')
                ->has('dateFormatted')
                ->has('dailyNoteContent')
                ->has('aiDigest')
                ->has('translations')
            )
        );
});
