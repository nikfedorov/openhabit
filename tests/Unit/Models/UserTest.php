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

test('to array', function (): void {
    $user = User::factory()->create()->refresh();

    expect(array_keys($user->toArray()))
        ->toBe([
            'id',
            'name',
            'telegram_id',
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

test('preferred locale returns locale when set', function (): void {
    $user = User::factory()->telegram()->create(['locale' => 'ru']);

    expect($user->preferredLocale())->toBe('ru');
});

test('preferred locale returns en when locale is null', function (): void {
    $user = User::factory()->create(['locale' => null]);

    expect($user->preferredLocale())->toBe('en');
});

// ─── Telegram ───────────────────────────────────────────────

test('routeNotificationForTelegram returns telegram_id', function (): void {
    $user = User::factory()->telegram()->create();

    expect($user->routeNotificationForTelegram())->toBe($user->telegram_id);
});

test('routeNotificationForTelegram returns null when no telegram_id', function (): void {
    $user = User::factory()->create(['telegram_id' => null]);

    expect($user->routeNotificationForTelegram())->toBeNull();
});

test('canReceiveTelegramNotifications returns true for valid telegram user', function (): void {
    $user = User::factory()->telegram()->create();

    expect($user->canReceiveTelegramNotifications())->toBeTrue();
});

test('canReceiveTelegramNotifications returns false when telegram_id is null', function (): void {
    $user = User::factory()->create(['telegram_id' => null]);

    expect($user->canReceiveTelegramNotifications())->toBeFalse();
});

test('canReceiveTelegramNotifications returns false when bot is blocked', function (): void {
    $user = User::factory()->telegram()->create(['telegram_bot_blocked_at' => now()]);

    expect($user->canReceiveTelegramNotifications())->toBeFalse();
});

test('canReceiveTelegramNotifications returns false when user is deleted', function (): void {
    $user = User::factory()->telegram()->create(['telegram_user_deleted_at' => now()]);

    expect($user->canReceiveTelegramNotifications())->toBeFalse();
});

// ─── Relationships ──────────────────────────────────────────

test('has many habits', function (): void {
    $user = User::factory()->create();
    Habit::factory()->for($user)->create();

    expect($user->habits)->toHaveCount(1);
});

test('has many categories', function (): void {
    $user = User::factory()->create();
    Category::factory()->for($user)->create();

    expect($user->categories)->toHaveCount(1);
});

test('has many habit completions', function (): void {
    $user = User::factory()->create();
    HabitCompletion::factory()->for($user)->create();

    expect($user->habitCompletions)->toHaveCount(1);
});

test('has many daily notes', function (): void {
    $user = User::factory()->create();
    DailyNote::factory()->for($user)->create();

    expect($user->dailyNotes)->toHaveCount(1);
});

test('has many stats', function (): void {
    $user = User::factory()->create();
    Stat::factory()->for($user)->create();

    expect($user->stats)->toHaveCount(1);
});

test('belongs to ai tone', function (): void {
    $tone = AiTone::factory()->create();
    $user = User::factory()->create(['ai_tone_id' => $tone->id]);

    expect($user->aiTone)->toBeInstanceOf(AiTone::class);
});

test('has one last digest', function (): void {
    $user = User::factory()->create();
    AiDigest::factory()->for($user)->create(['created_at' => now()->subDay()]);
    AiDigest::factory()->for($user)->create(['created_at' => now()]);

    expect($user->lastDigest)->toBeInstanceOf(AiDigest::class)
        ->and($user->lastDigest->created_at->toDateString())->toBe(now()->toDateString());
});

test('has many ai digests', function (): void {
    $user = User::factory()->create();
    AiDigest::factory()->for($user)->count(2)->create();

    expect($user->aiDigests)->toHaveCount(2);
});

test('has many ai logs', function (): void {
    $user = User::factory()->create();
    AiLog::factory()->for($user)->create();

    expect($user->aiLogs)->toHaveCount(1);
});

test('has many memories', function (): void {
    $user = User::factory()->create();
    UserMemory::factory()->for($user)->create();

    expect($user->memories)->toHaveCount(1);
});

test('has many payments', function (): void {
    $user = User::factory()->create();
    Payment::factory()->for($user)->create();

    expect($user->payments)->toHaveCount(1);
});

// ─── Premium Logic ──────────────────────────────────────────

test('hasPremium returns true with active subscription', function (): void {
    $user = User::factory()->premium()->create();

    expect($user->hasPremium())->toBeTrue();
});

test('hasPremium returns true during trial period', function (): void {
    Setting::factory()->create(['key' => 'trial_period_days', 'value' => '14']);
    $user = User::factory()->create(['subscription_expires_at' => null]);

    expect($user->hasPremium())->toBeTrue();
});

test('hasPremium returns false when trial expired and no subscription', function (): void {
    Setting::factory()->create(['key' => 'trial_period_days', 'value' => '14']);
    $user = User::factory()->trialExpired()->create(['subscription_expires_at' => null]);

    expect($user->hasPremium())->toBeFalse();
});

test('isTrialing returns true during trial without subscription', function (): void {
    Setting::factory()->create(['key' => 'trial_period_days', 'value' => '14']);
    $user = User::factory()->create(['subscription_expires_at' => null]);

    expect($user->isTrialing())->toBeTrue();
});

test('isTrialing returns false with active subscription', function (): void {
    $user = User::factory()->premium()->create();

    expect($user->isTrialing())->toBeFalse();
});

test('shouldShowTrialBanner returns true during trial without dismissal', function (): void {
    Setting::factory()->create(['key' => 'trial_period_days', 'value' => '14']);
    $user = User::factory()->create([
        'subscription_expires_at' => null,
        'trial_banner_dismissed_at' => null,
    ]);

    expect($user->shouldShowTrialBanner())->toBeTrue();
});

test('shouldShowTrialBanner returns false when dismissed', function (): void {
    Setting::factory()->create(['key' => 'trial_period_days', 'value' => '14']);
    $user = User::factory()->create([
        'subscription_expires_at' => null,
        'trial_banner_dismissed_at' => now(),
    ]);

    expect($user->shouldShowTrialBanner())->toBeFalse();
});

test('trialRemaining returns human readable string during trial', function (): void {
    Setting::factory()->create(['key' => 'trial_period_days', 'value' => '14']);
    $user = User::factory()->create(['subscription_expires_at' => null]);

    expect($user->trialRemaining())->toBeString()->not->toBeEmpty();
});

test('trialRemaining returns null when trial expired', function (): void {
    Setting::factory()->create(['key' => 'trial_period_days', 'value' => '14']);
    $user = User::factory()->trialExpired()->create();

    expect($user->trialRemaining())->toBeNull();
});

test('trialRemaining returns null when trial days is zero', function (): void {
    Setting::factory()->create(['key' => 'trial_period_days', 'value' => '0']);
    $user = User::factory()->create();

    expect($user->trialRemaining())->toBeNull();
});

// ─── Scopes ─────────────────────────────────────────────────

test('canReceiveTelegram scope filters correctly', function (): void {
    User::factory()->telegram()->create();
    User::factory()->create(['telegram_id' => null]);
    User::factory()->telegram()->create(['telegram_bot_blocked_at' => now()]);

    expect(User::query()->canReceiveTelegram()->count())->toBe(1);
});

test('withoutPremium scope filters users without active premium', function (): void {
    User::factory()->premium()->create();
    User::factory()->create(['subscription_expires_at' => null]);
    User::factory()->create(['subscription_expires_at' => now()->subDay()]);

    expect(User::query()->withoutPremium()->count())->toBe(2);
});
