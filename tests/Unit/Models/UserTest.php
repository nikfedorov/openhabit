<?php

declare(strict_types=1);
use App\Models\AiDigest;
use App\Models\AiLog;
use App\Models\AiTone;
use App\Models\Category;
use App\Models\DailyNote;
use App\Models\Habit;
use App\Models\HabitCompletion;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\Stat;
use App\Models\User;
use App\Models\UserMemory;

it('has expected keys in toArray', function (): void {
    $user = User::factory()->create()->refresh();

    expect(array_keys($user->toArray()))
        ->toBe([
            'id',
            'name',
            'telegram_id',
            'telegram_username',
            'telegram_bot_blocked_at',
            'telegram_user_deleted_at',
            'email',
            'email_verified_at',
            'is_admin',
            'subscription_expires_at',
            'theme',
            'timezone',
            'locale',
            'day_starts_at',
            'move_completed_to_end',
            'birthdate',
            'ai_digest_time',
            'ai_tone_id',
            'last_active_at',
            'trial_banner_dismissed_at',
            'created_at',
            'updated_at',
            'deleted_at',
        ]);
});

it('returns preferred locale when set', function (): void {
    $user = User::factory()->telegram()->create(['locale' => 'ru']);

    expect($user->preferredLocale())->toBe('ru');
});

it('returns preferred en when locale is null', function (): void {
    $user = User::factory()->create(['locale' => null]);

    expect($user->preferredLocale())->toBe('en');
});

// ─── Telegram ───────────────────────────────────────────────

it('returns telegram_id for routeNotificationForTelegram', function (): void {
    $user = User::factory()->telegram()->create();

    expect($user->routeNotificationForTelegram())->toBe($user->telegram_id);
});

it('returns null for routeNotificationForTelegram when no telegram_id', function (): void {
    $user = User::factory()->create(['telegram_id' => null]);

    expect($user->routeNotificationForTelegram())->toBeNull();
});

it('returns true for canReceiveTelegramNotifications for valid telegram user', function (): void {
    $user = User::factory()->telegram()->create();

    expect($user->canReceiveTelegramNotifications())->toBeTrue();
});

it('returns false for canReceiveTelegramNotifications when telegram_id is null', function (): void {
    $user = User::factory()->create(['telegram_id' => null]);

    expect($user->canReceiveTelegramNotifications())->toBeFalse();
});

it('returns false for canReceiveTelegramNotifications when bot is blocked', function (): void {
    $user = User::factory()->telegram()->create(['telegram_bot_blocked_at' => now()]);

    expect($user->canReceiveTelegramNotifications())->toBeFalse();
});

it('returns false for canReceiveTelegramNotifications when user is deleted', function (): void {
    $user = User::factory()->telegram()->create(['telegram_user_deleted_at' => now()]);

    expect($user->canReceiveTelegramNotifications())->toBeFalse();
});

// ─── Attributes ─────────────────────────────────────────────

it('returns telegram photo url when telegram_username is set', function (): void {
    $user = User::factory()->telegram()->create(['telegram_username' => 'johndoe']);

    expect($user->telegram_photo_url)->toBe('https://t.me/i/userpic/160/johndoe.jpg');
});

it('returns null for telegram_photo_url when telegram_username is null', function (): void {
    $user = User::factory()->create(['telegram_username' => null]);

    expect($user->telegram_photo_url)->toBeNull();
});

// ─── Relationships ──────────────────────────────────────────

it('has many habits', function (): void {
    $user = User::factory()->create();
    Habit::factory()->for($user)->create();

    expect($user->habits)->toHaveCount(1);
});

it('has many categories', function (): void {
    $user = User::factory()->create();
    Category::factory()->for($user)->create();

    expect($user->categories)->toHaveCount(1);
});

it('has many habit completions', function (): void {
    $user = User::factory()->create();
    HabitCompletion::factory()->for($user)->create();

    expect($user->habitCompletions)->toHaveCount(1);
});

it('has many daily notes', function (): void {
    $user = User::factory()->create();
    DailyNote::factory()->for($user)->create();

    expect($user->dailyNotes)->toHaveCount(1);
});

it('has many stats', function (): void {
    $user = User::factory()->create();
    Stat::factory()->for($user)->create();

    expect($user->stats)->toHaveCount(1);
});

it('belongs to ai tone', function (): void {
    $tone = AiTone::factory()->create();
    $user = User::factory()->create(['ai_tone_id' => $tone->id]);

    expect($user->aiTone)->toBeInstanceOf(AiTone::class);
});

it('has one last digest', function (): void {
    $user = User::factory()->create();
    AiDigest::factory()->for($user)->create(['created_at' => now()->subDay()]);
    AiDigest::factory()->for($user)->create(['created_at' => now()]);

    expect($user->lastDigest)->toBeInstanceOf(AiDigest::class)
        ->and($user->lastDigest->created_at->toDateString())->toBe(now()->toDateString());
});

it('has many ai digests', function (): void {
    $user = User::factory()->create();
    AiDigest::factory()->for($user)->count(2)->create();

    expect($user->aiDigests)->toHaveCount(2);
});

it('has many ai logs', function (): void {
    $user = User::factory()->create();
    AiLog::factory()->for($user)->create();

    expect($user->aiLogs)->toHaveCount(1);
});

it('has many memories', function (): void {
    $user = User::factory()->create();
    UserMemory::factory()->for($user)->create();

    expect($user->memories)->toHaveCount(1);
});

it('has many payments', function (): void {
    $user = User::factory()->create();
    Payment::factory()->for($user)->create();

    expect($user->payments)->toHaveCount(1);
});

// ─── Premium Logic ──────────────────────────────────────────

// ─── Scopes ─────────────────────────────────────────────────

it('filters correctly for canReceiveTelegram scope', function (): void {
    User::factory()->telegram()->create();
    User::factory()->create(['telegram_id' => null]);
    User::factory()->telegram()->create(['telegram_bot_blocked_at' => now()]);

    expect(User::query()->canReceiveTelegram()->count())->toBe(1);
});

it('filters correctly for withoutPremium scope', function (): void {
    Setting::factory()->create(['key' => 'trial_period_days', 'value' => '0']);

    User::factory()->premium()->create();
    User::factory()->create(['subscription_expires_at' => null]);
    User::factory()->create(['subscription_expires_at' => now()->subDay()]);

    expect(User::query()->withoutPremium()->count())->toBe(2);
});

// ─── currentDate ─────────────────────────────────────────────

it('currentDate returns calendar date in user timezone', function (): void {
    // 10 PM UTC = next calendar day in UTC, but previous day in UTC-5
    $this->travelTo(now()->setTimezone('UTC')->setTime(22, 0, 0));
    $user = User::factory()->create(['timezone' => 'UTC', 'day_starts_at' => '00:00:00']);

    expect($user->currentDate()->toDateString())->toBe(now()->toDateString());
});

it('currentDate returns previous day when current time is before day_starts_at', function (): void {
    $this->travelTo(now()->setTime(2, 0, 0)); // 2 AM UTC
    $user = User::factory()->create(['timezone' => 'UTC', 'day_starts_at' => '03:00:00']);

    expect($user->currentDate()->toDateString())->toBe(now()->subDay()->toDateString());
});

it('currentDate returns current day when current time equals day_starts_at', function (): void {
    $this->travelTo(now()->setTime(3, 0, 0)); // exactly 3 AM UTC
    $user = User::factory()->create(['timezone' => 'UTC', 'day_starts_at' => '03:00:00']);

    expect($user->currentDate()->toDateString())->toBe(now()->toDateString());
});

it('currentDate returns current day when current time is after day_starts_at', function (): void {
    $this->travelTo(now()->setTime(10, 0, 0)); // 10 AM UTC
    $user = User::factory()->create(['timezone' => 'UTC', 'day_starts_at' => '03:00:00']);

    expect($user->currentDate()->toDateString())->toBe(now()->toDateString());
});

it('currentDate considers user timezone together with day_starts_at', function (): void {
    // UTC is 8 AM, but user is UTC+3 so local time is 11 AM; day starts at 03:00 local
    $this->travelTo(now()->setTimezone('UTC')->setTime(8, 0, 0));
    $user = User::factory()->create(['timezone' => 'Europe/Moscow', 'day_starts_at' => '03:00:00']); // UTC+3

    // 11 AM local time, which is >= 03:00, so it's today in the user's timezone
    expect($user->currentDate()->toDateString())->toBe(now()->setTimezone('Europe/Moscow')->toDateString());
});

it('currentDate falls back to UTC when timezone is null', function (): void {
    $this->travelTo(now()->setTime(10, 0, 0));
    // Test the null guard in currentDate() by creating the user and then nulling the timezone in memory
    $user = User::factory()->create(['timezone' => 'UTC', 'day_starts_at' => '00:00:00']);
    $user->timezone = null;

    expect($user->currentDate()->toDateString())->toBe(now()->toDateString());
});
