<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\MemoryCategory;
use App\Models\User;
use App\Models\UserMemory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserMemory>
 */
final class UserMemoryFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'category' => fake()->randomElement(MemoryCategory::cases()),
            'content' => fake()->sentence(),
        ];
    }

    public function category(MemoryCategory $category): self
    {
        return $this->state([
            'category' => $category,
        ]);
    }
}
