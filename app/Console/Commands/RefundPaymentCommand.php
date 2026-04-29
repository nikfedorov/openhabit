<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Payments\RefundPaymentAction;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use SergiX44\Nutgram\Telegram\Exceptions\TelegramException;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\note;
use function Laravel\Prompts\search;

final class RefundPaymentCommand extends Command
{
    protected $signature = 'app:refund-payment {charge_id? : Telegram payment charge ID}';

    protected $description = 'Cancel a subscription and refund a payment by charge ID';

    /**
     * @var array<string, Payment>
     */
    private array $searchResultsByChargeId = [];

    public function handle(RefundPaymentAction $refundPayment): int
    {
        $this->searchResultsByChargeId = [];

        $chargeId = $this->argument('charge_id');

        $payment = $chargeId !== null
            ? $this->findByChargeId((string) $chargeId)
            : $this->selectInteractively();

        if (! $payment instanceof Payment) {
            return self::FAILURE;
        }

        $this->showPaymentDetails($payment);

        $cancelSubscription = $payment->is_recurring && confirm('Cancel subscription?', default: true);
        $issueRefund = confirm('Issue refund?', default: true);

        if (! $cancelSubscription && ! $issueRefund) {
            info('Nothing to do. Aborted.');

            return self::SUCCESS;
        }

        try {
            $refundPayment->handle($payment, $cancelSubscription, $issueRefund);
        } catch (TelegramException $telegramException) {
            error('Telegram API error: '.$telegramException->getMessage());

            return self::FAILURE;
        }

        $message = match (true) {
            $cancelSubscription && $issueRefund => '✓ Subscription cancelled and refund issued.',
            $cancelSubscription => '✓ Subscription cancelled (no refund).',
            default => '✓ Refund issued (subscription kept).',
        };

        info($message);

        return self::SUCCESS;
    }

    /**
     * Find a payment by its Telegram charge ID and validate it is refundable.
     */
    private function findByChargeId(string $chargeId): ?Payment
    {
        /** @var Payment|null $payment */
        $payment = $this->searchResultsByChargeId[$chargeId] ?? $this->paymentDetailsQuery()
            ->where('telegram_payment_charge_id', $chargeId)
            ->first();

        if ($payment === null) {
            error('No payment found with charge ID: '.$chargeId);

            return null;
        }

        if ($payment->isRefunded) {
            error('This payment has already been refunded on '.$payment->refunded_at?->toDateTimeString().'.');

            return null;
        }

        return $payment;
    }

    /**
     * Show an interactive search over active (non-refunded) payments.
     */
    private function selectInteractively(): ?Payment
    {
        $count = Payment::query()->whereNull('refunded_at')->count();

        if ($count === 0) {
            info('No active payments to refund.');

            return null;
        }

        /** @var string $chargeId */
        $chargeId = search(
            label: sprintf('Search active payments (%d total)', $count),
            options: fn (string $query): array => $this->searchOptions($query),
            placeholder: 'User name, charge ID, or amount…',
        );

        return $this->findByChargeId($chargeId);
    }

    /**
     * @return Builder<Payment>
     */
    private function paymentDetailsQuery(): Builder
    {
        return Payment::query()
            ->select([
                'id',
                'user_id',
                'telegram_payment_charge_id',
                'total_amount',
                'is_recurring',
                'refunded_at',
                'created_at',
            ])
            ->with(['user:id,name,telegram_id']);
    }

    /**
     * @return array<string, string>
     */
    private function searchOptions(string $query): array
    {
        $searchTerm = sprintf('%%%s%%', $query);

        $payments = $this->paymentDetailsQuery()
            ->whereNull('refunded_at')
            ->when($query !== '', function (Builder $paymentQuery) use ($searchTerm): void {
                $paymentQuery->where(function (Builder $innerQuery) use ($searchTerm): void {
                    $innerQuery
                        ->where('telegram_payment_charge_id', 'like', $searchTerm)
                        ->orWhereHas('user', fn (Builder $userQuery): Builder => $userQuery->where('name', 'like', $searchTerm));
                });
            })
            ->latest()
            ->limit(20)
            ->get();

        $searchResultsByChargeId = [];

        foreach ($payments as $payment) {
            $searchResultsByChargeId[$payment->telegram_payment_charge_id] = $payment;
        }

        $this->searchResultsByChargeId = $searchResultsByChargeId;

        return $payments->mapWithKeys(fn (Payment $payment): array => [
            $payment->telegram_payment_charge_id => $this->formatSearchResult($payment),
        ])->all();
    }

    private function formatSearchResult(Payment $payment): string
    {
        return sprintf(
            '%s  %s  %s  %s',
            mb_str_pad($payment->formattedAmount, 8),
            mb_str_pad($payment->created_at->toDateString(), 12),
            mb_str_pad($payment->is_recurring ? 'recurring' : 'one-time', 10),
            $payment->user instanceof User ? $payment->user->name : '(unknown)',
        );
    }

    /**
     * Print a summary of the payment before confirming.
     */
    private function showPaymentDetails(Payment $payment): void
    {
        /** @var User $user */
        $user = $payment->user;

        note(implode(PHP_EOL, [
            '  Payment details',
            '  ───────────────────────────────────────',
            '  Charge ID  : '.$payment->telegram_payment_charge_id,
            '  User       : '.$user->name.' (TG: '.($user->telegram_id ?? '—').')',
            '  Amount     : '.$payment->formattedAmount,
            '  Type       : '.($payment->is_recurring ? 'Recurring subscription' : 'One-time payment'),
            '  Created    : '.$payment->created_at->toDateTimeString(),
            '  ───────────────────────────────────────',
        ]));
    }
}
