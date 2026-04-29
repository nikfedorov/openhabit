<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
final class PaymentFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'currency' => 'XTR',
            'total_amount' => fake()->numberBetween(1, 10000),
            'invoice_payload' => 'premium',
            'subscription_expiration_date' => now()->addDays(30),
            'is_recurring' => true,
            'is_first_recurring' => true,
            'telegram_payment_charge_id' => 'charge_'.fake()->unique()->uuid(),
            'provider_payment_charge_id' => 'provider_'.fake()->unique()->uuid(),
            'refunded_at' => null,
        ];
    }

    public function refunded(): self
    {
        return $this->state([
            'refunded_at' => now(),
        ]);
    }
}
