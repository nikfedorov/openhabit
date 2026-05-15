<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\AiModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Laravel\Ai\Enums\Lab;

/**
 * @extends Factory<AiModel>
 */
final class AiModelFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(3, true),
            'slug' => fake()->unique()->slug(3),
            'provider' => Lab::OpenRouter,
            'priority' => fake()->numberBetween(0, 100),
            'is_active' => true,
        ];
    }

    public function inactive(): self
    {
        return $this->state(['is_active' => false]);
    }
}
