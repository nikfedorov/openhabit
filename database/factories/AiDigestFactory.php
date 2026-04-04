<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\AiDigest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AiDigest>
 */
final class AiDigestFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'date' => fake()->date(),
            'content' => fake()->paragraph(),
            'habits_data' => null,
        ];
    }

    public function forDate(string $date): self
    {
        return $this->state([
            'date' => $date,
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function withHabitsData(array $data = []): self
    {
        return $this->state([
            'habits_data' => $data,
        ]);
    }
}
