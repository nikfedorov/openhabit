<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Habit;
use App\Models\HabitCompletion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HabitCompletion>
 */
final class HabitCompletionFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'habit_id' => Habit::factory(),
            'user_id' => User::factory(),
            'completed_at' => fake()->dateTimeBetween('-7 days', 'now')->format('Y-m-d'),
            'current_iteration' => 1,
            'notes' => fake()->optional(0.3)->sentence(),
        ];
    }

    public function today(): static
    {
        return $this->state([
            'completed_at' => now()->toDateString(),
        ]);
    }

    public function forDate(string $date): static
    {
        return $this->state([
            'completed_at' => $date,
        ]);
    }

    public function withIteration(int $iteration): static
    {
        return $this->state([
            'current_iteration' => $iteration,
        ]);
    }
}
