<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\AiLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AiLog>
 */
final class AiLogFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'model' => 'google/gemma-3-27b-it:free',
            'system_prompt' => fake()->paragraph(),
            'user_prompt' => fake()->paragraph(),
            'response' => fake()->paragraph(),
            'input_tokens' => fake()->numberBetween(100, 2000),
            'output_tokens' => fake()->numberBetween(50, 1024),
            'duration_ms' => fake()->numberBetween(500, 15000),
            'is_successful' => true,
            'error' => null,
        ];
    }

    public function failed(string $error = 'Connection timeout'): self
    {
        return $this->state([
            'response' => null,
            'output_tokens' => null,
            'is_successful' => false,
            'error' => $error,
        ]);
    }
}
