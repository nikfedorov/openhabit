<?php

declare(strict_types=1);

namespace App\Actions\Settings;

use App\Models\Category;
use App\Models\DailyNote;
use App\Models\Habit;
use App\Models\HabitCompletion;
use App\Models\Payment;
use App\Models\Stat;
use App\Models\User;
use App\Models\UserMemory;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

/**
 * Exports all user data into a ZIP archive of CSV files.
 */
final class ExportUserDataAction
{
    /**
     * Export user data and return the relative path on the local disk.
     */
    public function handle(User $user): string
    {
        $disk = Storage::disk('local');
        $filename = sprintf('exports/user-%s-', $user->id).now()->format('Ymd-His').'.zip';
        $absolutePath = $disk->path($filename);

        $disk->makeDirectory('exports');

        $locale = $user->locale ?? 'en';

        $zip = new ZipArchive;
        $zip->open($absolutePath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $zip->addFromString('categories.csv', $this->buildCsv(
            ['id', 'name', 'description', 'slug', 'is_active', 'sort_order', 'created_at', 'updated_at'],
            $user->categories(),
            fn (Category $category): array => [
                $category->id,
                $category->getTranslation('name', $locale, false) ?? '',
                $category->getTranslation('description', $locale, false) ?? '',
                $category->slug,
                $category->is_active ? '1' : '0',
                $category->sort_order,
                $category->created_at->toIso8601String(),
                $category->updated_at->toIso8601String(),
            ],
        ));

        $zip->addFromString('habits.csv', $this->buildCsv(
            ['id', 'category_id', 'name', 'description', 'is_active', 'sort_order', 'iterations_required', 'rrule', 'created_at', 'updated_at', 'deleted_at'],
            $user->habits()->withTrashed(),
            fn (Habit $habit): array => [
                $habit->id,
                $habit->category_id,
                $habit->getTranslation('name', $locale, false) ?? '',
                $habit->getTranslation('description', $locale, false) ?? '',
                $habit->is_active ? '1' : '0',
                $habit->sort_order,
                $habit->iterations_required,
                $habit->rrule,
                $habit->created_at->toIso8601String(),
                $habit->updated_at->toIso8601String(),
                $habit->deleted_at?->toIso8601String(),
            ],
        ));

        $zip->addFromString('habit_completions.csv', $this->buildCsv(
            ['id', 'habit_id', 'completed_at', 'current_iteration', 'created_at', 'updated_at'],
            $user->habitCompletions(),
            fn (HabitCompletion $completion): array => [
                $completion->id,
                $completion->habit_id,
                $completion->completed_at->toIso8601String(),
                $completion->current_iteration,
                $completion->created_at->toIso8601String(),
                $completion->updated_at->toIso8601String(),
            ],
        ));

        $zip->addFromString('daily_notes.csv', $this->buildCsv(
            ['id', 'date', 'content', 'created_at', 'updated_at'],
            $user->dailyNotes(),
            fn (DailyNote $note): array => [
                $note->id,
                $note->date->format('Y-m-d'),
                $note->content,
                $note->created_at->toIso8601String(),
                $note->updated_at->toIso8601String(),
            ],
        ));

        $zip->addFromString('stats.csv', $this->buildCsv(
            ['id', 'period', 'period_start', 'planned_count', 'completed_count', 'created_at', 'updated_at'],
            $user->stats(),
            fn (Stat $stat): array => [
                $stat->id,
                $stat->period->value,
                Date::parse($stat->period_start)->format('Y-m-d'),
                $stat->planned_count,
                $stat->completed_count,
                $stat->created_at->toIso8601String(),
                $stat->updated_at->toIso8601String(),
            ],
        ));

        $zip->addFromString('user_memories.csv', $this->buildCsv(
            ['id', 'category', 'content', 'created_at', 'updated_at'],
            $user->memories(),
            fn (UserMemory $memory): array => [
                $memory->id,
                $memory->category->value,
                $memory->content,
                $memory->created_at->toIso8601String(),
                $memory->updated_at->toIso8601String(),
            ],
        ));

        $zip->addFromString('payments.csv', $this->buildCsv(
            ['id', 'total_amount', 'currency', 'is_recurring', 'subscription_expiration_date', 'refunded_at', 'created_at', 'updated_at'],
            $user->payments(),
            fn (Payment $payment): array => [
                $payment->id,
                $payment->total_amount,
                $payment->currency,
                $payment->is_recurring ? '1' : '0',
                $payment->subscription_expiration_date?->toIso8601String(),
                $payment->refunded_at?->toIso8601String(),
                $payment->created_at->toIso8601String(),
                $payment->updated_at->toIso8601String(),
            ],
        ));

        $zip->close();

        return $filename;
    }

    /**
     * Build a CSV string by streaming a relationship query to a temporary buffer.
     *
     * @template TModel of Model
     * @template TParent of Model
     *
     * @param  array<int, string>  $headers
     * @param  HasMany<TModel, TParent>  $query
     * @param  Closure(TModel): array<int, mixed>  $mapRow
     */
    private function buildCsv(array $headers, HasMany $query, Closure $mapRow): string
    {
        $handle = fopen('php://temp', 'r+');

        if ($handle === false) {
            return ''; // @codeCoverageIgnore
        }

        fputcsv($handle, $headers, escape: '\\');

        foreach ($query->cursor() as $record) {
            /** @var TModel $record */
            /** @var array<int, bool|float|int|string|null> $row */
            $row = $mapRow($record);
            fputcsv($handle, $row, escape: '\\');
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return $content !== false ? $content : '';
    }
}
