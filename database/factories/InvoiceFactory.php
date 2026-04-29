<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
final class InvoiceFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'slug' => fake()->unique()->slug(2),
            'stars' => fake()->numberBetween(100, 5000),
            'subscription_period' => User::PREMIUM_PERIOD_SECONDS,
            'invoice_link' => null,
        ];
    }
}
