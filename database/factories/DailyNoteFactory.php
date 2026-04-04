<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\DailyNote;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DailyNote>
 */
final class DailyNoteFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'date' => fake()->dateTimeBetween('-7 days', 'now')->format('Y-m-d'),
            'content' => fake()->paragraph(),
        ];
    }

    public function today(): static
    {
        return $this->state([
            'date' => now()->toDateString(),
        ]);
    }

    public function forDate(string $date): static
    {
        return $this->state([
            'date' => $date,
        ]);
    }
}
