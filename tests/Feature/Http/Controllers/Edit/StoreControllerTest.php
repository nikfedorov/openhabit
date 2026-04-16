<?php

declare(strict_types=1);

use App\Models\Habit;
use App\Models\HabitNotification;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;

/**
 * @return array<string, mixed>
 */
function habitPayload(array $overrides = []): array
{
    return array_merge([
        'name' => 'Test habit',
        'description' => null,
        'frequency' => 'DAILY',
        'iterations_required' => 1,
        'is_active' => true,
        'weekly_days' => [],
        'monthly_days' => [],
        'monthly_mode' => 'day',
        'monthly_position' => 1,
        'monthly_weekday' => 0,
        'notifications' => [],
    ], $overrides);
}

it('requires authentication', function (): void {
    $this->postJson('/api/edit/habits')
        ->assertUnauthorized();
});

it('creates a daily habit with correct response structure', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user, 'sanctum')
        ->postJson('/api/edit/habits', habitPayload([
            'name' => 'Morning run',
            'description' => 'Run 5km',
        ]))
        ->assertOk()
        ->assertJson(fn (AssertableJson $json): AssertableJson => $json
            ->has('data', 1, fn (AssertableJson $json): AssertableJson => $json
                ->where('name', 'Morning run')
                ->where('description', 'Run 5km')
                ->where('is_active', true)
                ->where('iterations_required', 1)
                ->has('id')
                ->has('sort_order')
                ->has('human_readable')
                ->has('is_franklin_virtue')
            )
        );

    $this->assertDatabaseHas('habits', [
        'user_id' => $user->id,
        'rrule' => 'FREQ=DAILY',
        'iterations_required' => 1,
    ]);
});

it('creates habits with correct rrule', function (array $overrides, string $expectedRRule): void {
    $user = User::factory()->create();

    $this->actingAs($user, 'sanctum')
        ->postJson('/api/edit/habits', habitPayload($overrides))
        ->assertOk();

    $this->assertDatabaseHas('habits', [
        'user_id' => $user->id,
        'rrule' => $expectedRRule,
    ]);
})->with([
    'weekly with specific days' => [
        ['name' => 'Gym', 'frequency' => 'WEEKLY', 'weekly_days' => [0, 2, 4]],
        'FREQ=WEEKLY;BYDAY=MO,WE,FR',
    ],
    'monthly by specific days' => [
        ['name' => 'Pay bills', 'frequency' => 'MONTHLY', 'monthly_days' => [1, 15]],
        'FREQ=MONTHLY;BYMONTHDAY=1,15',
    ],
    'monthly by position' => [
        ['name' => 'Team meeting', 'frequency' => 'MONTHLY', 'monthly_mode' => 'position', 'monthly_position' => 2, 'monthly_weekday' => 1],
        'FREQ=MONTHLY;BYDAY=2TU',
    ],
]);

it('creates a habit with notifications', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user, 'sanctum')
        ->postJson('/api/edit/habits', habitPayload([
            'name' => 'Meditate',
            'notifications' => [
                ['time' => '08:00', 'is_active' => true],
                ['time' => '20:00', 'is_active' => false],
            ],
        ]))
        ->assertOk();

    $habit = Habit::query()->where('user_id', $user->id)->first();

    $this->assertDatabaseHas('habit_notifications', [
        'habit_id' => $habit->id,
        'time' => '08:00',
        'is_active' => true,
    ]);

    $this->assertDatabaseHas('habit_notifications', [
        'habit_id' => $habit->id,
        'time' => '20:00',
        'is_active' => false,
    ]);
});

it('updates an existing habit and replaces notifications', function (): void {
    $user = User::factory()->create();
    $habit = Habit::factory()->daily()->create(['user_id' => $user->id]);
    HabitNotification::factory()->create(['habit_id' => $habit->id, 'time' => '09:00']);

    $this->actingAs($user, 'sanctum')
        ->putJson('/api/edit/habits/'.$habit->id, habitPayload([
            'name' => 'Updated name',
            'description' => 'Updated desc',
            'frequency' => 'WEEKLY',
            'iterations_required' => 3,
            'is_active' => false,
            'weekly_days' => [0, 4],
            'notifications' => [
                ['time' => '14:30', 'is_active' => true],
            ],
        ]))
        ->assertOk()
        ->assertJson(fn (AssertableJson $json): AssertableJson => $json
            ->has('data', fn (AssertableJson $json): AssertableJson => $json
                ->where('name', 'Updated name')
                ->where('description', 'Updated desc')
                ->where('iterations_required', 3)
                ->where('is_active', false)
                ->etc()
            )
        );

    $this->assertDatabaseHas('habits', [
        'id' => $habit->id,
        'rrule' => 'FREQ=WEEKLY;BYDAY=MO,FR',
        'iterations_required' => 3,
        'is_active' => false,
    ]);

    $this->assertDatabaseMissing('habit_notifications', [
        'habit_id' => $habit->id,
        'time' => '09:00',
    ]);

    $this->assertDatabaseHas('habit_notifications', [
        'habit_id' => $habit->id,
        'time' => '14:30',
        'is_active' => true,
    ]);
});

it('prevents unauthorized modifications', function (): void {
    $user = User::factory()->create();
    $otherHabit = Habit::factory()->daily()->create();
    $franklinHabit = Habit::factory()->daily()->franklinVirtue()->create(['user_id' => $user->id]);

    $this->actingAs($user, 'sanctum')
        ->putJson('/api/edit/habits/'.$otherHabit->id, habitPayload(['name' => 'Hacked']))
        ->assertForbidden();

    $this->actingAs($user, 'sanctum')
        ->putJson('/api/edit/habits/'.$franklinHabit->id, habitPayload(['name' => 'Changed']))
        ->assertForbidden();
});

it('validates request data', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user, 'sanctum')
        ->postJson('/api/edit/habits', [])
        ->assertJsonValidationErrors(['name', 'frequency', 'iterations_required', 'is_active', 'monthly_mode', 'monthly_position', 'monthly_weekday']);

    $this->actingAs($user, 'sanctum')
        ->postJson('/api/edit/habits', habitPayload([
            'frequency' => 'INVALID',
            'notifications' => [
                ['time' => '08:17', 'is_active' => true],
            ],
        ]))
        ->assertJsonValidationErrors(['frequency', 'notifications.0.time']);
});
