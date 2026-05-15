<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\AiModel;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\search;

#[Signature('app:remove-ai-model')]
#[Description('Remove an AI model from the database')]
final class RemoveAiModelCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $count = AiModel::query()->count();

        if ($count === 0) {
            info('No AI models found.');

            return self::SUCCESS;
        }

        $slug = search(
            label: sprintf('Search AI models (%d total)', $count),
            options: fn (string $query): array => $this->searchOptions($query),
            placeholder: 'Model name or slug…',
        );

        /** @var AiModel|null $model */
        $model = AiModel::query()->where('slug', $slug)->first();

        if (! $model instanceof AiModel) {
            error('Model not found.');

            return self::FAILURE;
        }

        $this->showModelDetails($model);

        if (! confirm(sprintf('Delete model "%s"? This cannot be undone.', $model->name), default: false)) {
            info('Aborted.');

            return self::SUCCESS;
        }

        $model->delete();

        info(sprintf('✓ AI model "%s" deleted successfully.', $model->name));

        return self::SUCCESS;
    }

    /**
     * Return search options keyed by slug for use in the search prompt.
     *
     * @return array<string, string>
     */
    private function searchOptions(string $query): array
    {
        $searchTerm = sprintf('%%%s%%', $query);

        return AiModel::query()
            ->when($query !== '', fn (Builder $q) => $q->where(function (Builder $inner) use ($searchTerm): void {
                $inner->where('name', 'like', $searchTerm)
                    ->orWhere('slug', 'like', $searchTerm);
            }))
            ->orderBy('priority')
            ->orderBy('name')
            ->limit(20)
            ->get()
            ->mapWithKeys(fn (AiModel $model): array => [
                $model->slug => $this->formatSearchResult($model),
            ])
            ->all();
    }

    /**
     * Format a single model for display in the search results.
     */
    private function formatSearchResult(AiModel $model): string
    {
        return sprintf(
            '%s  %s  %s',
            mb_str_pad($model->name, 30),
            mb_str_pad($model->slug, 30),
            $model->is_active ? 'active' : 'inactive',
        );
    }

    /**
     * Print model details before confirming deletion.
     */
    private function showModelDetails(AiModel $model): void
    {
        $this->line(implode(PHP_EOL, [
            '',
            '  Model details',
            '  ─────────────────────────────────────',
            '  Name     : '.$model->name,
            '  Slug     : '.$model->slug,
            '  Provider : '.$model->provider->value,
            '  Priority : '.$model->priority,
            '  Status   : '.($model->is_active ? 'active' : 'inactive'),
            '',
        ]));
    }
}
