<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\SettingType;
use App\Models\Setting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Setting>
 */
final class SettingFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'key' => fake()->unique()->slug(),
            'type' => SettingType::String,
            'value' => fake()->sentence(),
        ];
    }

    public function boolean(): self
    {
        return $this->state([
            'type' => SettingType::Boolean,
            'value' => fake()->randomElement(['0', '1']),
        ]);
    }

    public function number(): self
    {
        return $this->state([
            'type' => SettingType::Number,
            'value' => (string) fake()->randomFloat(2, 1, 1000),
        ]);
    }

    public function text(): self
    {
        return $this->state([
            'type' => SettingType::Text,
            'value' => fake()->paragraphs(2, true),
        ]);
    }
}
