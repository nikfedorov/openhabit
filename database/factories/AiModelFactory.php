<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\AiModel;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'priority' => fake()->numberBetween(0, 100),
            'is_active' => true,
            'is_free' => false,
        ];
    }

    public function inactive(): self
    {
        return $this->state(['is_active' => false]);
    }

    public function free(): self
    {
        return $this->state(['is_free' => true]);
    }
}
