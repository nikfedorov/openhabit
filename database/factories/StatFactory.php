<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\StatPeriod;
use App\Models\Stat;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Stat>
 */
final class StatFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $planned = fake()->numberBetween(1, 10);
        $completed = fake()->numberBetween(0, $planned);

        return [
            'user_id' => User::factory(),
            'period' => StatPeriod::Daily,
            'period_start' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'planned_count' => $planned,
            'completed_count' => $completed,
        ];
    }

    public function daily(): static
    {
        return $this->state([
            'period' => StatPeriod::Daily,
        ]);
    }

    public function weekly(): static
    {
        return $this->state(function (): array {
            $planned = fake()->numberBetween(7, 70);

            return [
                'period' => StatPeriod::Weekly,
                'period_start' => now()->startOfWeek()->toDateString(),
                'planned_count' => $planned,
                'completed_count' => fake()->numberBetween(0, $planned),
            ];
        });
    }

    public function yearly(): static
    {
        return $this->state(function (): array {
            $planned = fake()->numberBetween(100, 500);

            return [
                'period' => StatPeriod::Yearly,
                'period_start' => now()->subYear()->startOfWeek()->toDateString(),
                'planned_count' => $planned,
                'completed_count' => fake()->numberBetween(0, $planned),
            ];
        });
    }

    public function today(): static
    {
        return $this->state([
            'period' => StatPeriod::Daily,
            'period_start' => now()->toDateString(),
        ]);
    }

    public function currentWeek(): static
    {
        return $this->state([
            'period' => StatPeriod::Weekly,
            'period_start' => now()->startOfWeek()->toDateString(),
        ]);
    }

    public function forDate(string $date): static
    {
        return $this->state([
            'period' => StatPeriod::Daily,
            'period_start' => $date,
        ]);
    }

    public function forWeek(int $year, int $weekNumber): static
    {
        $date = now()->setISODate($year, $weekNumber)->startOfWeek();

        return $this->state([
            'period' => StatPeriod::Weekly,
            'period_start' => $date->toDateString(),
        ]);
    }

    public function perfect(): static
    {
        return $this->state(fn (array $attributes): array => [
            'completed_count' => $attributes['planned_count'],
        ]);
    }

    public function none(): static
    {
        return $this->state([
            'completed_count' => 0,
        ]);
    }

    public function empty(): static
    {
        return $this->state([
            'planned_count' => 0,
            'completed_count' => 0,
        ]);
    }
}
