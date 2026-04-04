<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Habit;
use App\Models\HabitNotification;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HabitNotification>
 */
final class HabitNotificationFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        /** @var int $minutes */
        $minutes = fake()->randomElement([0, 15, 30, 45]);

        return [
            'habit_id' => Habit::factory(),
            'time' => sprintf('%02d:%02d', fake()->numberBetween(6, 22), $minutes),
            'is_active' => true,
        ];
    }
}
