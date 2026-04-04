<?php

declare(strict_types=1);

use App\Models\Habit;
use App\Models\HabitNotification;
use App\Models\User;
use App\Notifications\HabitReminderNotification;
use Illuminate\Support\Facades\Notification;

test('sends notification within time window', function (): void {
    Notification::fake();

    $this->travelTo(today()->addHours(9));

    $user = User::factory()->telegram()->create(['timezone' => 'UTC']);
    $habit = Habit::factory()->for($user)->create([
        'is_active' => true,
        'rrule' => 'FREQ=DAILY',
    ]);
    HabitNotification::factory()->for($habit)->create([
        'time' => '09:00',
        'is_active' => true,
        'last_notified_at' => null,
    ]);

    $this->artisan('app:send-notifications')
        ->expectsOutputToContain('Notifications dispatched: 1')
        ->assertExitCode(0);

    Notification::assertSentTo($user, HabitReminderNotification::class);
});

test('skips notification outside time window', function (): void {
    Notification::fake();

    $this->travelTo(today()->addHours(10));

    $user = User::factory()->telegram()->create(['timezone' => 'UTC']);
    $habit = Habit::factory()->for($user)->create(['is_active' => true, 'rrule' => 'FREQ=DAILY']);
    HabitNotification::factory()->for($habit)->create([
        'time' => '08:00',
        'is_active' => true,
    ]);

    $this->artisan('app:send-notifications')
        ->expectsOutputToContain('Notifications dispatched: 0')
        ->assertExitCode(0);

    Notification::assertNothingSent();
});

test('skips already notified today', function (): void {
    Notification::fake();

    $this->travelTo(today()->addHours(9));

    $user = User::factory()->telegram()->create(['timezone' => 'UTC']);
    $habit = Habit::factory()->for($user)->create(['is_active' => true, 'rrule' => 'FREQ=DAILY']);
    HabitNotification::factory()->for($habit)->create([
        'time' => '09:00',
        'is_active' => true,
        'last_notified_at' => now(),
    ]);

    $this->artisan('app:send-notifications')
        ->expectsOutputToContain('Notifications dispatched: 0')
        ->assertExitCode(0);

    Notification::assertNothingSent();
});

test('skips when rrule does not match today', function (): void {
    Notification::fake();

    $this->travelTo(today()->addHours(9));

    $user = User::factory()->telegram()->create(['timezone' => 'UTC']);
    $dayOfWeek = now()->dayOfWeekIso;
    $differentDay = $dayOfWeek === 1 ? 'TU' : 'MO';

    $habit = Habit::factory()->for($user)->create([
        'is_active' => true,
        'rrule' => 'FREQ=WEEKLY;BYDAY='.$differentDay,
    ]);
    HabitNotification::factory()->for($habit)->create([
        'time' => '09:00',
        'is_active' => true,
    ]);

    $this->artisan('app:send-notifications')
        ->expectsOutputToContain('Notifications dispatched: 0')
        ->assertExitCode(0);

    Notification::assertNothingSent();
});

test('sends when habit has no rrule', function (): void {
    Notification::fake();

    $this->travelTo(today()->addHours(9));

    $user = User::factory()->telegram()->create(['timezone' => 'UTC']);
    $habit = Habit::factory()->for($user)->create([
        'is_active' => true,
        'rrule' => null,
    ]);
    HabitNotification::factory()->for($habit)->create([
        'time' => '09:00',
        'is_active' => true,
        'last_notified_at' => null,
    ]);

    $this->artisan('app:send-notifications')
        ->expectsOutputToContain('Notifications dispatched: 1')
        ->assertExitCode(0);

    Notification::assertSentTo($user, HabitReminderNotification::class);
});

test('updates last_notified_at after sending', function (): void {
    Notification::fake();

    $this->travelTo(today()->addHours(9));

    $user = User::factory()->telegram()->create(['timezone' => 'UTC']);
    $habit = Habit::factory()->for($user)->create(['is_active' => true, 'rrule' => 'FREQ=DAILY']);
    $notification = HabitNotification::factory()->for($habit)->create([
        'time' => '09:00',
        'is_active' => true,
        'last_notified_at' => null,
    ]);

    $this->artisan('app:send-notifications')->assertExitCode(0);

    expect($notification->refresh()->last_notified_at)->not->toBeNull();
});

test('skips inactive habit', function (): void {
    Notification::fake();

    $this->travelTo(today()->addHours(9));

    $user = User::factory()->telegram()->create(['timezone' => 'UTC']);
    $habit = Habit::factory()->for($user)->create(['is_active' => false, 'rrule' => 'FREQ=DAILY']);
    HabitNotification::factory()->for($habit)->create([
        'time' => '09:00',
        'is_active' => true,
    ]);

    $this->artisan('app:send-notifications')
        ->expectsOutputToContain('Notifications dispatched: 0')
        ->assertExitCode(0);

    Notification::assertNothingSent();
});
