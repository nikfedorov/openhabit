<?php

declare(strict_types=1);

use App\Models\Habit;
use App\Models\HabitNotification;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;

it('requires authentication', function (): void {
    $habit = Habit::factory()->daily()->create();

    $this->getJson('/api/edit/habits/'.$habit->id)
        ->assertUnauthorized();
});

it('returns habit with parsed form data for daily habit', function (): void {
    $user = User::factory()->create();
    $habit = Habit::factory()->daily()->create([
        'user_id' => $user->id,
        'iterations_required' => 2,
    ]);

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/edit/habits/'.$habit->id)
        ->assertOk()
        ->assertJson(fn (AssertableJson $json): AssertableJson => $json
            ->has('navigationTranslations')
            ->has('settings')
            ->has('habitTranslations')
            ->has('data', fn (AssertableJson $json): AssertableJson => $json
                ->where('id', $habit->id)
                ->has('name')
                ->has('description')
                ->where('iterations_required', 2)
                ->where('is_active', true)
                ->where('frequency', 'DAILY')
                ->has('weekly_days')
                ->has('monthly_days')
                ->has('monthly_mode')
                ->has('monthly_position')
                ->has('monthly_weekday')
                ->has('notifications')
            )
        );
});

it('parses rrule frequency data correctly', function (string $rrule, array $expected): void {
    $user = User::factory()->create();
    $habit = Habit::factory()->create([
        'user_id' => $user->id,
        'rrule' => $rrule,
    ]);

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/edit/habits/'.$habit->id)
        ->assertOk()
        ->assertJson(fn (AssertableJson $json): AssertableJson => $json
            ->has('data', fn (AssertableJson $json): AssertableJson => $json
                ->where('frequency', $expected['frequency'])
                ->where($expected['key'], $expected['value'])
                ->etc()
            )
            ->etc()
        );
})->with([
    'weekly days' => ['FREQ=WEEKLY;BYDAY=MO,WE,FR', ['frequency' => 'WEEKLY', 'key' => 'weekly_days', 'value' => [0, 2, 4]]],
    'monthly by day' => ['FREQ=MONTHLY;BYMONTHDAY=1,15', ['frequency' => 'MONTHLY', 'key' => 'monthly_days', 'value' => [1, 15]]],
    'monthly by position' => ['FREQ=MONTHLY;BYDAY=2TU', ['frequency' => 'MONTHLY', 'key' => 'monthly_mode', 'value' => 'position']],
]);

it('includes notifications sorted by time', function (): void {
    $user = User::factory()->create();
    $habit = Habit::factory()->daily()->create(['user_id' => $user->id]);

    HabitNotification::factory()->create(['habit_id' => $habit->id, 'time' => '14:00', 'is_active' => true]);
    HabitNotification::factory()->create(['habit_id' => $habit->id, 'time' => '08:00', 'is_active' => false]);

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/edit/habits/'.$habit->id)
        ->assertOk()
        ->assertJson(fn (AssertableJson $json): AssertableJson => $json
            ->has('data', fn (AssertableJson $json): AssertableJson => $json
                ->has('notifications', 2)
                ->where('notifications.0.time', '08:00')
                ->where('notifications.0.is_active', false)
                ->where('notifications.1.time', '14:00')
                ->where('notifications.1.is_active', true)
                ->etc()
            )
            ->etc()
        );
});

it('prevents unauthorized access', function (): void {
    $user = User::factory()->create();
    $otherHabit = Habit::factory()->daily()->create();
    $franklinHabit = Habit::factory()->daily()->franklinVirtue()->create(['user_id' => $user->id]);

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/edit/habits/'.$otherHabit->id)
        ->assertForbidden();

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/edit/habits/'.$franklinHabit->id)
        ->assertForbidden();
});
