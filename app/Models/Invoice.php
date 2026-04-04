<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\InvoiceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

/**
 * @property-read int $id
 * @property-read string $title
 * @property-read string $description
 * @property-read int $stars
 * @property-read int $subscription_period
 * @property-read string|null $invoice_link
 * @property-read string $slug
 */
final class Invoice extends Model
{
    /** @use HasFactory<InvoiceFactory> */
    use HasFactory;

    use HasTranslations;

    public $timestamps = false;

    /** @var array<int, string> */
    public array $translatable = ['title', 'description', 'invoice_link'];

    /**
     * Get the premium invoice link for the current locale.
     */
    public static function premiumLink(): ?string
    {
        /** @var Invoice|null $invoice */
        $invoice = self::query()->where('slug', 'premium')->first();

        return $invoice?->invoice_link;
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'stars' => 'integer',
            'subscription_period' => 'integer',
        ];
    }
}
