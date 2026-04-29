<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\InvoiceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
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
     * Cached per locale to avoid repeated DB queries across requests.
     * Value is wrapped in an array to allow caching null (no invoice found).
     */
    public static function premiumLink(): ?string
    {
        $key = sprintf('invoice:premium:link:%s', app()->getLocale());

        /** @var array{link: string|null} $cached */
        $cached = Cache::remember($key, 3600, function (): array {
            /** @var Invoice|null $invoice */
            $invoice = self::query()->where('slug', 'premium')->first();

            return ['link' => $invoice?->invoice_link];
        });

        return $cached['link'];
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
