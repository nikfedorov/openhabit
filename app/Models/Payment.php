<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\PaymentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read int $id
 * @property-read string $user_id
 * @property-read string $telegram_payment_charge_id
 * @property-read string $provider_payment_charge_id
 * @property-read int $total_amount
 * @property-read string $currency
 * @property-read string|null $invoice_payload
 * @property-read CarbonInterface|null $subscription_expiration_date
 * @property-read bool $is_recurring
 * @property-read bool $is_first_recurring
 * @property-read CarbonInterface|null $refunded_at
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 * @property-read bool $isRefunded
 * @property-read string $formattedAmount
 */
final class Payment extends Model
{
    /** @use HasFactory<PaymentFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if this payment has been refunded.
     */
    protected function getIsRefundedAttribute(): bool
    {
        return $this->refunded_at !== null;
    }

    /**
     * Get a formatted display of the payment amount in stars.
     */
    protected function getFormattedAmountAttribute(): string
    {
        return $this->total_amount.' ⭐';
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'user_id' => 'string',
            'total_amount' => 'integer',
            'subscription_expiration_date' => 'datetime',
            'is_recurring' => 'boolean',
            'is_first_recurring' => 'boolean',
            'refunded_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
