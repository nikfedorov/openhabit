<?php

declare(strict_types=1);

use App\Models\AiModel;

it('informs when no ai models exist', function (): void {
    $this->artisan('app:remove-ai-model')
        ->expectsPromptsInfo('No AI models found.')
        ->assertSuccessful();
});

it('deletes a model after confirmation', function (): void {
    $model = AiModel::factory()->create(['name' => 'GPT-4o', 'slug' => 'gpt-4o', 'is_active' => true]);

    $this->artisan('app:remove-ai-model')
        ->expectsSearch(
            'Search AI models (1 total)',
            search: 'GPT',
            answers: [$model->slug => removeAiModelSearchAnswer($model)],
            answer: $model->slug,
        )
        ->expectsConfirmation('Delete model "GPT-4o"? This cannot be undone.', 'yes')
        ->expectsPromptsInfo('✓ AI model "GPT-4o" deleted successfully.')
        ->assertSuccessful();

    expect(AiModel::query()->where('slug', 'gpt-4o')->exists())->toBeFalse();
});

it('aborts when user declines confirmation', function (): void {
    $model = AiModel::factory()->create(['name' => 'GPT-4o', 'slug' => 'gpt-4o']);

    $this->artisan('app:remove-ai-model')
        ->expectsSearch(
            'Search AI models (1 total)',
            search: 'gpt',
            answers: [$model->slug => removeAiModelSearchAnswer($model)],
            answer: $model->slug,
        )
        ->expectsConfirmation('Delete model "GPT-4o"? This cannot be undone.', 'no')
        ->expectsPromptsInfo('Aborted.')
        ->assertSuccessful();

    expect(AiModel::query()->where('slug', 'gpt-4o')->exists())->toBeTrue();
});

it('fails when selected slug no longer exists in the database', function (): void {
    $model = AiModel::factory()->create(['name' => 'GPT-4o', 'slug' => 'gpt-4o']);

    $this->artisan('app:remove-ai-model')
        ->expectsSearch(
            'Search AI models (1 total)',
            search: 'gpt',
            answers: [$model->slug => removeAiModelSearchAnswer($model)],
            answer: 'non-existent-slug',
        )
        ->expectsPromptsError('Model not found.')
        ->assertFailed();
});

function removeAiModelSearchAnswer(AiModel $model): string
{
    return sprintf(
        '%s  %s  %s',
        mb_str_pad($model->name, 30),
        mb_str_pad($model->slug, 30),
        $model->is_active ? 'active' : 'inactive',
    );
}
