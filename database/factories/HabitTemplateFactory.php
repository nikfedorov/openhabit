<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\HabitTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HabitTemplate>
 */
final class HabitTemplateFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_template_id' => null,
            'name' => ['en' => fake()->sentence(3)],
            'description' => ['en' => fake()->optional()->paragraph()],
            'is_active' => true,
            'copy_by_default' => true,
            'show_in_templates' => true,
            'sort_order' => fake()->numberBetween(1, 100),
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

    public function withoutSchedule(): static
    {
        return $this->state([
            'rrule' => null,
        ]);
    }

    public function withEmptyRrule(): static
    {
        return $this->state([
            'rrule' => '',
        ]);
    }

    public function daily(int $interval = 1): static
    {
        return $this->state([
            'rrule' => $interval > 1 ? 'FREQ=DAILY;INTERVAL='.$interval : 'FREQ=DAILY',
        ]);
    }

    public function weekly(string $days = 'MO,TU,WE,TH,FR'): static
    {
        return $this->state([
            'rrule' => 'FREQ=WEEKLY;BYDAY='.$days,
        ]);
    }

    public function monthlyByDay(string $days = '1,15'): static
    {
        return $this->state([
            'rrule' => 'FREQ=MONTHLY;BYMONTHDAY='.$days,
        ]);
    }

    public function monthlyByWeekday(int $ordinal = 1, string $dayCode = 'SU'): static
    {
        return $this->state([
            'rrule' => sprintf('FREQ=MONTHLY;BYDAY=%d%s', $ordinal, $dayCode),
        ]);
    }

    public function yearly(string $weeks = '1,26,52'): static
    {
        return $this->state([
            'rrule' => sprintf('FREQ=YEARLY;BYWEEKNO=%s;BYDAY=MO,TU,WE,TH,FR,SA,SU', $weeks),
        ]);
    }
}
