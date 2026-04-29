<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Category;
use App\Models\Habit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Habit>
 */
final class HabitFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'category_id' => null,
            'name' => ['en' => fake()->randomElement([
                'Morning exercise',
                'Read a book',
                'Meditation',
                'Drink water',
                'Take a walk',
                'Learn a language',
                'Write a journal',
            ])],
            'description' => ['en' => fake()->optional()->sentence()],
            'is_active' => true,
            'sort_order' => 1,
            'iterations_required' => 1,
            'rrule' => 'FREQ=WEEKLY;BYDAY=MO,TU,WE,TH,FR',
        ];
    }

    public function inactive(): static
    {
        return $this->state([
            'is_active' => false,
        ]);
    }

    public function daily(): static
    {
        return $this->state([
            'rrule' => 'FREQ=DAILY',
        ]);
    }

    public function franklinVirtue(): static
    {
        return $this->state(fn (): array => [
            'category_id' => Category::factory()->franklinVirtues(),
        ]);
    }
}
