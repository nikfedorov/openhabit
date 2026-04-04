<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Models\User;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Console\Command;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Exceptions\TelegramException;
use SergiX44\Nutgram\Telegram\Types\Payment\LabeledPrice;

final class GenerateInvoiceLinksCommand extends Command
{
    protected $signature = 'app:generate-invoice-links';

    protected $description = 'Generate Telegram Stars invoice links for all invoices';

    public function handle(Nutgram $bot): int
    {
        $invoices = Invoice::query()->get();

        if ($invoices->isEmpty()) {
            $this->warn('No invoices found.');

            return self::SUCCESS;
        }

        /** @var array<int, string> $locales */
        $locales = config('translatable.locales');

        foreach ($invoices as $invoice) {
            $this->generateLinks($bot, $invoice, $locales);
        }

        return self::SUCCESS;
    }

    /**
     * @param  array<int, string>  $locales
     */
    private function generateLinks(Nutgram $bot, Invoice $invoice, array $locales): void
    {
        /** @var string $title */
        $title = $invoice->getTranslation('title', 'en');

        $this->info(sprintf('Generating invoice links for: %s...', $title));

        foreach ($locales as $locale) {
            $this->generateLinkForLocale($bot, $invoice, $locale);
        }

        $invoice->save();
    }

    private function generateLinkForLocale(Nutgram $bot, Invoice $invoice, string $locale): void
    {
        /** @var string $title */
        $title = $invoice->getTranslation('title', $locale);
        /** @var string $description */
        $description = $invoice->getTranslation('description', $locale);

        try {
            $link = $bot->createInvoiceLink(
                title: $title,
                description: $description,
                payload: $invoice->slug,
                provider_token: '',
                currency: 'XTR',
                prices: [LabeledPrice::make($title, $invoice->stars)],
                subscription_period: User::PREMIUM_PERIOD_SECONDS,
            );

            if ($link === null) {
                $this->error(sprintf('Failed to generate link for: %s (%s)', $title, $locale));

                return;
            }

            $invoice->setTranslation('invoice_link', $locale, $link);

            $this->info(sprintf('Link generated [%s]: %s', $locale, $link));
        } catch (ConnectException) {
            $this->error('Failed to connect to Telegram API. Check your network connection.');
        } catch (TelegramException $e) {
            $this->error('Telegram API error: '.$e->getMessage());
        }
    }
}
