<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Theme;
use App\Models\AiTone;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
final class UserFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => 'password',
            'is_admin' => false,
            'subscription_expires_at' => null,
            'theme' => Theme::System,
            'timezone' => null,
            'locale' => null,
            'day_starts_at' => '03:00:00',
            'move_completed_to_end' => true,
            'birthdate' => fake()->dateTimeBetween('-50 years', '-18 years')->format('Y-m-d'),
            'ai_digest_time' => null,
            'ai_tone_id' => AiTone::defaultId(...),
            'last_active_at' => null,
            'trial_banner_dismissed_at' => null,
            'remember_token' => Str::random(10),
        ];
    }

    public function admin(): self
    {
        return $this->state([
            'is_admin' => true,
        ]);
    }

    public function premium(): self
    {
        return $this->state([
            'subscription_expires_at' => now()->addDays(30),
        ]);
    }

    public function unverified(): self
    {
        return $this->state([
            'email_verified_at' => null,
        ]);
    }

    public function telegram(): self
    {
        return $this->state(fn (array $attributes): array => [
            'telegram_id' => (string) fake()->unique()->randomNumber(9),
            'telegram_username' => fake()->userName(),
            'locale' => fake()->randomElement(['en', 'ru', 'uk']),
            'email' => null,
            'email_verified_at' => null,
            'password' => null,
        ]);
    }

    public function telegramId(?int $telegramId = null): self
    {
        return $this->state([
            'telegram_id' => $telegramId ?? (int) fake()->unique()->numerify('########'),
        ]);
    }

    public function withAiDigest(string $time = '09:00', ?AiTone $tone = null): self
    {
        return $this->state(fn (): array => [
            'ai_digest_time' => $time,
            'ai_tone_id' => $tone instanceof AiTone ? $tone->id : AiTone::defaultId(),
        ]);
    }

    public function trialExpired(): self
    {
        return $this->state(fn (): array => [
            'created_at' => now()->subDays(Setting::trialPeriodDays() + 1),
        ]);
    }
}
