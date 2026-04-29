<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\AiTone;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AiTone>
 */
final class AiToneFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'slug' => fake()->unique()->slug(2),
            'name' => fake()->word(),
            'description' => fake()->sentence(),
            'icon' => 'sun',
            'system_instruction' => fake()->paragraph(),
            'sort_order' => 0,
            'is_active' => true,
        ];
    }

    public function friendly(): self
    {
        return $this->state([
            'slug' => 'friendly',
            'name' => 'Friendly',
            'description' => 'Warm and encouraging',
            'icon' => 'sun',
            'system_instruction' => 'Be casual, warm, and encouraging. Celebrate small wins and use a positive, upbeat tone. Use light humor when appropriate. Make the user feel good about their progress.',
            'sort_order' => 0,
        ]);
    }
}
