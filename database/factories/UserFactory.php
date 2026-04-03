<?php

declare(strict_types=1);

namespace Database\Factories;

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
            'remember_token' => Str::random(10),
        ];
    }

    public function telegram(): self
    {
        return $this->state(fn (array $attributes): array => [
            'telegram_id' => (string) fake()->unique()->randomNumber(9),
            'locale' => fake()->randomElement(['en', 'ru', 'uk']),
            'email' => null,
            'email_verified_at' => null,
            'password' => null,
        ]);
    }

    public function unverified(): self
    {
        return $this->state(fn (array $attributes): array => [
            'email_verified_at' => null,
        ]);
    }
}
