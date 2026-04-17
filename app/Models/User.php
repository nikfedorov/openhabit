<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Theme;
use App\Observers\UserObserver;
use Carbon\CarbonInterface;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property-read string $id
 * @property-read string|null $telegram_id
 * @property string|null $name
 * @property-read string|null $email
 * @property-read CarbonInterface|null $email_verified_at
 * @property-read string|null $password
 * @property string|null $locale
 * @property CarbonInterface|null $last_active_at
 * @property-read string|null $remember_token
 * @property-read CarbonInterface|null $telegram_bot_blocked_at
 * @property-read CarbonInterface|null $telegram_user_deleted_at
 * @property-read bool $is_admin
 * @property-read CarbonInterface|null $subscription_expires_at
 * @property-read Theme|null $theme
 * @property-read string|null $timezone
 * @property-read string|null $day_starts_at
 * @property-read bool $move_completed_to_end
 * @property-read CarbonInterface|null $birthdate
 * @property-read string|null $ai_digest_time
 * @property-read int|null $ai_tone_id
 * @property-read CarbonInterface|null $trial_banner_dismissed_at
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 * @property-read CarbonInterface|null $deleted_at
 */
#[ObservedBy(UserObserver::class)]
final class User extends Authenticatable implements HasLocalePreference, MustVerifyEmail
{
    use HasApiTokens;

    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use HasUuids;
    use Notifiable;
    use SoftDeletes;

    /**
     * 30 days in seconds (for premium subscription period).
     */
    public const int PREMIUM_PERIOD_SECONDS = 2_592_000;

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Route notifications for the Telegram channel.
     */
    public function routeNotificationForTelegram(): ?string
    {
        return $this->telegram_id;
    }

    /**
     * Whether the user can receive Telegram notifications.
     */
    public function canReceiveTelegramNotifications(): bool
    {
        return $this->telegram_id !== null
            && $this->telegram_bot_blocked_at === null
            && $this->telegram_user_deleted_at === null;
    }

    public function preferredLocale(): string
    {
        return $this->locale ?? 'en';
    }

    // ─── Relationships ──────────────────────────────────────────

    /**
     * @return HasMany<Habit, $this>
     */
    public function habits(): HasMany
    {
        return $this->hasMany(Habit::class);
    }

    /**
     * @return HasMany<Category, $this>
     */
    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    /**
     * @return HasMany<HabitCompletion, $this>
     */
    public function habitCompletions(): HasMany
    {
        return $this->hasMany(HabitCompletion::class);
    }

    /**
     * @return HasMany<DailyNote, $this>
     */
    public function dailyNotes(): HasMany
    {
        return $this->hasMany(DailyNote::class);
    }

    /**
     * @return HasMany<Stat, $this>
     */
    public function stats(): HasMany
    {
        return $this->hasMany(Stat::class);
    }

    /**
     * @return BelongsTo<AiTone, $this>
     */
    public function aiTone(): BelongsTo
    {
        return $this->belongsTo(AiTone::class);
    }

    /**
     * @return HasOne<AiDigest, $this>
     */
    public function lastDigest(): HasOne
    {
        return $this->hasOne(AiDigest::class)->latestOfMany();
    }

    /**
     * @return HasMany<AiDigest, $this>
     */
    public function aiDigests(): HasMany
    {
        return $this->hasMany(AiDigest::class);
    }

    /**
     * @return HasMany<AiLog, $this>
     */
    public function aiLogs(): HasMany
    {
        return $this->hasMany(AiLog::class);
    }

    /**
     * @return HasMany<UserMemory, $this>
     */
    public function memories(): HasMany
    {
        return $this->hasMany(UserMemory::class);
    }

    /**
     * @return HasMany<Payment, $this>
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'id' => 'string',
            'telegram_id' => 'string',
            'name' => 'string',
            'email' => 'string',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'locale' => 'string',
            'last_active_at' => 'datetime',
            'remember_token' => 'string',
            'telegram_bot_blocked_at' => 'datetime',
            'telegram_user_deleted_at' => 'datetime',
            'is_admin' => 'boolean',
            'subscription_expires_at' => 'datetime',
            'theme' => Theme::class,
            'move_completed_to_end' => 'boolean',
            'birthdate' => 'date',
            'ai_tone_id' => 'integer',
            'trial_banner_dismissed_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    // ─── Scopes ─────────────────────────────────────────────────

    /**
     * @param  Builder<User>  $query
     * @return Builder<User>
     */
    #[Scope]
    protected function canReceiveTelegram(Builder $query): Builder
    {
        return $query->whereNotNull('telegram_id')
            ->whereNull('telegram_bot_blocked_at')
            ->whereNull('telegram_user_deleted_at');
    }

    /**
     * @param  Builder<User>  $query
     * @return Builder<User>
     */
    #[Scope]
    protected function withoutPremium(Builder $query): Builder
    {
        return $query->where(function (Builder $q): void {
            $q->whereNull('subscription_expires_at')
                ->orWhere('subscription_expires_at', '<', now());
        });
    }
}
