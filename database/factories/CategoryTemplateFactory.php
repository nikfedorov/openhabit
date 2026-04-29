<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\CategoryTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CategoryTemplate>
 */
final class CategoryTemplateFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => ['en' => fake()->unique()->word()],
            'description' => ['en' => fake()->optional()->sentence()],
            'slug' => fake()->unique()->slug(2),
            'icon' => fake()->optional()->emoji(),
            'is_active' => true,
            'copy_by_default' => true,
            'sort_order' => fake()->numberBetween(1, 100),
        ];
    }

    public function inactive(): static
    {
        return $this->state([
            'is_active' => false,
        ]);
    }
}
